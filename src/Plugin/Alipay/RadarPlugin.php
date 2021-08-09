<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay;

use Closure;
use Psr\Http\Message\RequestInterface;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Logger;
use Albert\Payment\Pay;
use Albert\Payment\Provider\Alipay;
use Albert\Payment\Request;
use Albert\Payment\Rocket;

class RadarPlugin implements PluginInterface
{
    /**
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     */
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        Logger::info('[alipay][RadarPlugin] 插件开始装载', ['rocket' => $rocket]);

        $rocket->setRadar($this->getRequest($rocket));

        Logger::info('[alipay][RadarPlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $next($rocket);
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    protected function getRequest(Rocket $rocket): RequestInterface
    {
        return new Request(
            $this->getMethod($rocket),
            $this->getUrl($rocket),
            $this->getHeaders(),
            $this->getBody($rocket),
        );
    }

    protected function getMethod(Rocket $rocket): string
    {
        return strtoupper($rocket->getParams()['_method'] ?? 'POST');
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    protected function getUrl(Rocket $rocket): string
    {
        $config = get_alipay_config($rocket->getParams());

        return Alipay::URL[$config->get('mode', Pay::MODE_NORMAL)];
    }

    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
    }

    protected function getBody(Rocket $rocket): string
    {
        return $rocket->getPayload()->query();
    }
}
