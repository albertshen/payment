<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat;

use Closure;
use Psr\Http\Message\RequestInterface;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Logger;
use Albert\Payment\Request;
use Albert\Payment\Rocket;

abstract class GeneralPlugin implements PluginInterface
{
    /**
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     */
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        Logger::info('[wechat][GeneralPlugin] 通用插件开始装载', ['rocket' => $rocket]);

        $rocket->setRadar($this->getRequest($rocket));
        $this->doSomething($rocket);

        Logger::info('[wechat][GeneralPlugin] 通用插件装载完毕', ['rocket' => $rocket]);

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
            $this->getMethod(),
            $this->getUrl($rocket),
            $this->getHeaders(),
        );
    }

    protected function getMethod(): string
    {
        return 'POST';
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    protected function getUrl(Rocket $rocket): string
    {
        return get_wechat_base_uri($rocket->getParams()).
            $this->getUri($rocket);
    }

    protected function getHeaders(): array
    {
        return [
            'Accept' => 'application/json, text/plain, application/x-gzip',
            'User-Agent' => 'yansongda/pay-v3.0.0',
            'Content-Type' => 'application/json; charset=utf-8',
        ];
    }

    abstract protected function doSomething(Rocket $rocket): void;

    abstract protected function getUri(Rocket $rocket): string;
}
