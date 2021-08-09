<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay;

use Closure;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Exception\InvalidResponseException;
use Albert\Payment\Logger;
use Albert\Payment\Parser\NoHttpRequestParser;
use Albert\Payment\Rocket;
use Albert\Payment\Supports\Collection;
use Albert\Payment\Supports\Str;

class CallbackPlugin implements PluginInterface
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Albert\Payment\Exception\InvalidResponseException
     */
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        Logger::info('[alipay][CallbackPlugin] 插件开始装载', ['rocket' => $rocket]);

        $this->formatPayload($rocket);

        if (!($rocket->getParams()['sign'] ?? false)) {
            throw new InvalidResponseException(InvalidResponseException::INVALID_RESPONSE_SIGN, '', $rocket->getParams());
        }

        verify_alipay_sign($rocket->getParams(), $this->getSignContent($rocket->getPayload()), base64_decode($rocket->getParams()['sign']));

        $rocket->setDirection(NoHttpRequestParser::class)
            ->setDestination($rocket->getPayload());

        Logger::info('[alipay][CallbackPlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $next($rocket);
    }

    protected function formatPayload(Rocket $rocket): void
    {
        $payload = (new Collection($rocket->getParams()))->filter(function ($v, $k) {
            return '' !== $v && !is_null($v) && 'sign' != $k && 'sign_type' != $k && !Str::startsWith($k, '_');
        });

        $rocket->setPayload($payload);
    }

    protected function getSignContent(Collection $payload): string
    {
        return urldecode($payload->sortKeys()->toString());
    }
}
