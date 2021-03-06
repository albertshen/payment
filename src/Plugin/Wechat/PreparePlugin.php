<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat;

use Closure;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Logger;
use Albert\Payment\Rocket;
use Albert\Payment\Supports\Str;

class PreparePlugin implements PluginInterface
{
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        Logger::info('[wechat][PreparePlugin] 插件开始装载', ['rocket' => $rocket]);

        $rocket->mergePayload($this->getPayload($rocket->getParams()));

        Logger::info('[wechat][PreparePlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $next($rocket);
    }

    protected function getPayload(array $params): array
    {
        return array_filter($params, function ($v, $k) {
            return !Str::startsWith(strval($k), '_');
        }, ARRAY_FILTER_USE_BOTH);
    }
}
