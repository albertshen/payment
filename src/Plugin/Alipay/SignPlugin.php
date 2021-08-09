<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay;

use Closure;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Exception\InvalidConfigException;
use Albert\Payment\Logger;
use Albert\Payment\Rocket;
use Albert\Payment\Supports\Str;

class SignPlugin implements PluginInterface
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        Logger::info('[alipay][SignPlugin] 插件开始装载', ['rocket' => $rocket]);

        $this->formatPayload($rocket);

        $sign = $this->getSign($rocket);

        $rocket->mergePayload(['sign' => $sign]);

        Logger::info('[alipay][SignPlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $next($rocket);
    }

    protected function formatPayload(Rocket $rocket): void
    {
        $payload = $rocket->getPayload()->filter(function ($v, $k) {
            return '' !== $v && !is_null($v) && 'sign' != $k;
        });

        $contents = array_filter($payload->get('biz_content', []), function ($v, $k) {
            return !Str::startsWith(strval($k), '_');
        }, ARRAY_FILTER_USE_BOTH);

        $rocket->setPayload(
            $payload->merge(['biz_content' => json_encode($contents)])
        );
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    protected function getSign(Rocket $rocket): string
    {
        $privateKey = $this->getPrivateKey($rocket->getParams());

        $content = $rocket->getPayload()->sortKeys()->toString();

        openssl_sign($content, $sign, $privateKey, OPENSSL_ALGO_SHA256);

        $sign = base64_encode($sign);

        !is_resource($privateKey) ?: openssl_free_key($privateKey);

        return $sign;
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     *
     * @return resource|string
     */
    protected function getPrivateKey(array $params)
    {
        $privateKey = get_alipay_config($params)->get('app_secret_cert');

        if (is_null($privateKey)) {
            throw new InvalidConfigException(InvalidConfigException::ALIPAY_CONFIG_ERROR, 'Missing Alipay Config -- [app_secret_cert]');
        }

        return get_public_or_private_cert($privateKey);
    }
}
