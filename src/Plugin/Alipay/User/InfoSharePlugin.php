<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\User;

use Closure;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Logger;
use Albert\Payment\Rocket;

class InfoSharePlugin implements PluginInterface
{
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        Logger::info('[alipay][InfoSharePlugin] 插件开始装载', ['rocket' => $rocket]);

        $rocket->mergePayload([
            'method' => 'alipay.user.info.share',
            'auth_token' => $rocket->getParams()['auth_token'] ?? '',
        ]);

        Logger::info('[alipay][InfoSharePlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $next($rocket);
    }
}
