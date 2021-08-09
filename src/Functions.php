<?php

declare(strict_types=1);

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ServerRequestInterface;
use Albert\Payment\Contract\ConfigInterface;
use Albert\Payment\Exception\InvalidConfigException;
use Albert\Payment\Exception\InvalidResponseException;
use Albert\Payment\Parser\NoHttpRequestParser;
use Albert\Payment\Pay;
use Albert\Payment\Plugin\ParserPlugin;
use Albert\Payment\Plugin\Wechat\PreparePlugin;
use Albert\Payment\Plugin\Wechat\SignPlugin;
use Albert\Payment\Plugin\Wechat\WechatPublicCertsPlugin;
use Albert\Payment\Provider\Wechat;
use Albert\Payment\Rocket;
use Albert\Payment\Supports\Config;
use Albert\Payment\Supports\Str;
use Albert\Payment\Supports\Arr;
use Albert\Payment\Supports\Collection;

if (!function_exists('should_do_http_request')) {
    function should_do_http_request(Rocket $rocket): bool
    {
        $direction = $rocket->getDirection();

        return is_null($direction) ||
            (NoHttpRequestParser::class !== $direction &&
            !in_array(NoHttpRequestParser::class, class_parents($direction)));
    }
}

if (!function_exists('get_alipay_config')) {
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    function get_alipay_config(array $params = []): Config
    {
        $alipay = Pay::get(ConfigInterface::class)->get('alipay');

        $config = $params['_config'] ?? 'default';

        return new Config($alipay[$config] ?? []);
    }
}

if (!function_exists('get_public_or_private_cert')) {
    /**
     * @param bool $publicKey 是否公钥
     *
     * @return resource|string
     */
    function get_public_or_private_cert(string $key, bool $publicKey = false)
    {
        if ($publicKey) {
            return Str::endsWith($key, ['.crt', '.pem']) ? file_get_contents($key) : $key;
        }

        if (Str::endsWith($key, ['.crt', '.pem'])) {
            return openssl_pkey_get_private(
                Str::startsWith($key, 'file://') ? $key : 'file://'.$key
            );
        }

        return "-----BEGIN RSA PRIVATE KEY-----\n".
            wordwrap($key, 64, "\n", true).
            "\n-----END RSA PRIVATE KEY-----";
    }
}

if (!function_exists('verify_alipay_sign')) {
    /**
     * @param string $sign base64decode 之后的
     *
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Albert\Payment\Exception\InvalidResponseException
     */
    function verify_alipay_sign(array $params, string $contents, string $sign): void
    {
        $public = get_alipay_config($params)->get('alipay_public_cert_path');

        if (empty($public)) {
            throw new InvalidConfigException(InvalidConfigException::ALIPAY_CONFIG_ERROR, 'Missing Alipay Config -- [alipay_public_cert_path]');
        }

        $result = 1 === openssl_verify(
            $contents,
            $sign,
            get_public_or_private_cert($public, true),
            OPENSSL_ALGO_SHA256);

        if (!$result) {
            throw new InvalidResponseException(InvalidResponseException::INVALID_RESPONSE_SIGN, '', func_get_args());
        }
    }
}

if (!function_exists('get_wechat_config')) {
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    function get_wechat_config(array $params): Config
    {
        $wechat = Pay::get(ConfigInterface::class)->get('wechat');

        $config = $params['_config'] ?? 'default';

        return new Config($wechat[$config] ?? []);
    }
}

if (!function_exists('get_wechat_base_uri')) {
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    function get_wechat_base_uri(array $params): string
    {
        $config = get_wechat_config($params);

        return Wechat::URL[$config->get('mode', Pay::MODE_NORMAL)];
    }
}

if (!function_exists('get_wechat_authorization')) {
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     */
    function get_wechat_authorization(array $params, int $timestamp, string $random, string $contents): string
    {
        $config = get_wechat_config($params);
        $mchPublicCertPath = $config->get('mch_public_cert_path');

        if (empty($mchPublicCertPath)) {
            throw new InvalidConfigException(InvalidConfigException::WECHAT_CONFIG_ERROR, 'Missing Wechat Config -- [mch_public_cert_path]');
        }

        $ssl = openssl_x509_parse(get_public_or_private_cert($mchPublicCertPath, true));

        if (empty($ssl['serialNumberHex'])) {
            throw new InvalidConfigException(InvalidConfigException::WECHAT_CONFIG_ERROR, 'Parse [mch_public_cert_path] Serial Number Error');
        }

        $auth = sprintf(
            'mchid="%s",nonce_str="%s",timestamp="%d",serial_no="%s",signature="%s"',
            $config->get('mch_id', ''),
            $random,
            $timestamp,
            $ssl['serialNumberHex'],
            get_wechat_sign($params, $contents),
        );

        return 'WECHATPAY2-SHA256-RSA2048 '.$auth;
    }
}

if (!function_exists('get_wechat_sign')) {
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     */
    function get_wechat_sign(array $params, string $contents): string
    {
        $privateKey = get_wechat_config($params)->get('mch_secret_cert');

        if (empty($privateKey)) {
            throw new InvalidConfigException(InvalidConfigException::WECHAT_CONFIG_ERROR, 'Missing Wechat Config -- [mch_secret_cert]');
        }

        $privateKey = get_public_or_private_cert($privateKey);

        openssl_sign($contents, $sign, $privateKey, 'sha256WithRSAEncryption');

        $sign = base64_encode($sign);

        !is_resource($privateKey) ?: openssl_free_key($privateKey);

        return $sign;
    }
}

if (!function_exists('verify_wechat_sign')) {
    /**
     * @param \Psr\Http\Message\ServerRequestInterface|\Psr\Http\Message\ResponseInterface $message
     *
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     * @throws \Albert\Payment\Exception\InvalidResponseException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Albert\Payment\Exception\InvalidParamsException
     */
    function verify_wechat_sign(MessageInterface $message, array $params): void
    {
        if ($message instanceof ServerRequestInterface && 'localhost' === $message->getUri()->getHost()) {
            return;
        }

        $wechatSerial = $message->getHeaderLine('Wechatpay-Serial');
        $timestamp = $message->getHeaderLine('Wechatpay-Timestamp');
        $random = $message->getHeaderLine('Wechatpay-Nonce');
        $sign = $message->getHeaderLine('Wechatpay-Signature');
        $body = $message->getBody()->getContents();

        $content = $timestamp."\n".$random."\n".$body."\n";
        $public = get_wechat_config($params)->get('wechat_public_cert_path.'.$wechatSerial);

        if (empty($sign)) {
            throw new InvalidResponseException(InvalidResponseException::INVALID_RESPONSE_SIGN, '', ['headers' => $message->getHeaders(), 'body' => $body]);
        }

        $public = get_public_or_private_cert(
            empty($public) ? reload_wechat_public_certs($params, $wechatSerial) : $public,
            true
        );

        $result = 1 === openssl_verify(
            $content,
            base64_decode($sign),
            get_public_or_private_cert($public, true),
            'sha256WithRSAEncryption'
        );

        if (!$result) {
            throw new InvalidResponseException(InvalidResponseException::INVALID_RESPONSE_SIGN, '', ['headers' => $message->getHeaders(), 'body' => $body]);
        }
    }
}

if (!function_exists('reload_wechat_public_certs')) {
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Albert\Payment\Exception\InvalidParamsException
     * @throws \Albert\Payment\Exception\InvalidResponseException
     */
    function reload_wechat_public_certs(array $params, string $serialNo): string
    {
        $wechat = Pay::wechat();
        $data = $wechat->pay(
            [PreparePlugin::class, WechatPublicCertsPlugin::class, SignPlugin::class, ParserPlugin::class],
            $params
        )->get('data', []);

        foreach ($data as $item) {
            $certs[$item['serial_no']] = decrypt_wechat_resource($item['encrypt_certificate'], $params)['ciphertext'] ?? '';
        }

        $wechatConfig = get_wechat_config($params);
        $wechatConfig['wechat_public_cert_path'] = ((array) $wechatConfig['wechat_public_cert_path']) + ($certs ?? []);

        Pay::set(ConfigInterface::class, Pay::get(ConfigInterface::class)->merge([
            'wechat' => [$params['_config'] ?? 'default' => $wechatConfig->all()],
        ]));

        if (empty($certs[$serialNo])) {
            throw new InvalidConfigException(InvalidConfigException::WECHAT_CONFIG_ERROR, 'Get Wechat Public Cert Error');
        }

        return $certs[$serialNo];
    }
}

if (!function_exists('decrypt_wechat_resource')) {
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     * @throws \Albert\Payment\Exception\InvalidResponseException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    function decrypt_wechat_resource(array $resource, array $params): array
    {
        $ciphertext = base64_decode($resource['ciphertext'] ?? '');
        $secret = get_wechat_config($params)->get('mch_secret_key');

        if (strlen($ciphertext) <= Wechat::AUTH_TAG_LENGTH_BYTE) {
            throw new InvalidResponseException(InvalidResponseException::INVALID_CIPHERTEXT_PARAMS);
        }

        if (is_null($secret) || Wechat::MCH_SECRET_KEY_LENGTH_BYTE != strlen($secret)) {
            throw new InvalidConfigException(InvalidConfigException::WECHAT_CONFIG_ERROR, 'Missing Wechat Config -- [mch_secret_key]');
        }

        switch ($resource['algorithm'] ?? '') {
            case 'AEAD_AES_256_GCM':
                $resource['ciphertext'] = decrypt_wechat_resource_aes_256_gcm($ciphertext, $secret, $resource['nonce'] ?? '', $resource['associated_data'] ?? '');
                break;
            default:
                throw new InvalidResponseException(InvalidResponseException::INVALID_REQUEST_ENCRYPTED_METHOD);
        }

        return $resource;
    }
}

if (!function_exists('decrypt_wechat_resource_aes_256_gcm')) {
    /**
     * @throws \Albert\Payment\Exception\InvalidResponseException
     *
     * @return array|string
     */
    function decrypt_wechat_resource_aes_256_gcm(string $ciphertext, string $secret, string $nonce, string $associatedData)
    {
        $decrypted = openssl_decrypt(
            substr($ciphertext, 0, -Wechat::AUTH_TAG_LENGTH_BYTE),
            'aes-256-gcm',
            $secret,
            OPENSSL_RAW_DATA,
            $nonce,
            substr($ciphertext, -Wechat::AUTH_TAG_LENGTH_BYTE),
            $associatedData
        );

        if ('certificate' !== $associatedData) {
            $decrypted = json_decode($decrypted, true);

            if (JSON_ERROR_NONE !== json_last_error()) {
                throw new InvalidResponseException(InvalidResponseException::INVALID_REQUEST_ENCRYPTED_DATA);
            }
        }

        return $decrypted;
    }
}

if (!function_exists('collect')) {
    /**
     * Create a collection from the given value.
     */
    function collect(array $value = []): Collection
    {
        return new Collection($value);
    }
}

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @param mixed $value
     *
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

if (!function_exists('data_get')) {
    /**
     * Get an item from an array or object using "dot" notation.
     *
     * @param array|int|string|null $key
     * @param mixed|null            $default
     * @param mixed                 $target
     */
    function data_get($target, $key, $default = null)
    {
        if (is_null($key)) {
            return $target;
        }

        $key = is_array($key) ? $key : explode('.', is_int($key) ? (string) $key : $key);
        while (!is_null($segment = array_shift($key))) {
            if ('*' === $segment) {
                if ($target instanceof Collection) {
                    $target = $target->all();
                } elseif (!is_array($target)) {
                    return value($default);
                }
                $result = [];
                foreach ($target as $item) {
                    $result[] = data_get($item, $key);
                }

                return in_array('*', $key) ? Arr::collapse($result) : $result;
            }
            if (Arr::accessible($target) && Arr::exists($target, $segment)) {
                $target = $target[$segment];
            } elseif (is_object($target) && isset($target->{$segment})) {
                $target = $target->{$segment};
            } else {
                return value($default);
            }
        }

        return $target;
    }
}
