<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay;

use Closure;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Exception\InvalidResponseException;
use Albert\Payment\Logger;
use Albert\Payment\Rocket;
use Albert\Payment\Supports\Collection;

class LaunchPlugin implements PluginInterface
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     * @throws \Albert\Payment\Exception\InvalidResponseException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        /* @var Rocket $rocket */
        $rocket = $next($rocket);

        Logger::info('[alipay][LaunchPlugin] 插件开始装载', ['rocket' => $rocket]);

        if (should_do_http_request($rocket)) {
            $this->verifySign($rocket);

            $rocket->setDestination($this->getMethodResponse($rocket));
        }

        Logger::info('[alipay][LaunchPlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $rocket;
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     * @throws \Albert\Payment\Exception\InvalidResponseException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    protected function verifySign(Rocket $rocket): void
    {
        $response = $rocket->getDestination()->get($this->getResponseKey($rocket));
        $sign = $rocket->getDestination()->get('sign', '');

        if ('' === $sign || is_null($response)) {
            throw new InvalidResponseException(InvalidResponseException::INVALID_RESPONSE_SIGN, '', $response);
        }

        verify_alipay_sign($rocket->getParams(), json_encode($response, JSON_UNESCAPED_UNICODE), base64_decode($sign));
    }

    protected function getMethodResponse(Rocket $rocket): Collection
    {
        return Collection::wrap(
            $rocket->getDestination()->get($this->getResponseKey($rocket))
        );
    }

    protected function getResponseKey(Rocket $rocket): string
    {
        $method = $rocket->getPayload()->get('method');

        return str_replace('.', '_', $method).'_response';
    }
}
