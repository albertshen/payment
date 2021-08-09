<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Ebpp;

use Closure;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Logger;
use Albert\Payment\Rocket;

class PdeductPayPlugin implements PluginInterface
{
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        Logger::info('[alipay][PdeductPayPlugin] 插件开始装载', ['rocket' => $rocket]);

        $rocket->mergePayload([
            'method' => 'alipay.ebpp.pdeduct.pay',
            'biz_content' => array_merge(
                [
                    'agent_channel' => 'PUBLICFORM',
                ],
                $rocket->getParams(),
            ),
        ]);

        Logger::info('[alipay][PdeductPayPlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $next($rocket);
    }
}
