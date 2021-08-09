<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat;

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
     * @throws \Albert\Payment\Exception\InvalidParamsException
     */
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        /* @var Rocket $rocket */
        $rocket = $next($rocket);

        Logger::info('[wechat][LaunchPlugin] 插件开始装载', ['rocket' => $rocket]);

        if (should_do_http_request($rocket)) {
            verify_wechat_sign($rocket->getDestinationOrigin(), $rocket->getParams());

            $rocket->setDestination($this->formatResponse($rocket));
        }

        Logger::info('[wechat][LaunchPlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $rocket;
    }

    /**
     * @throws \Albert\Payment\Exception\InvalidResponseException
     */
    protected function formatResponse(Rocket $rocket): Collection
    {
        $response = $rocket->getDestination();

        $code = $response->get('code');

        if (!is_null($code) && 0 != $code) {
            throw new InvalidResponseException(InvalidResponseException::INVALID_RESPONSE_CODE);
        }

        return $response;
    }
}
