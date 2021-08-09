<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Ebpp;

use Closure;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Logger;
use Albert\Payment\Rocket;

class PdeductSignAddPlugin implements PluginInterface
{
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        Logger::info('[alipay][PdeductSignAddPlugin] 插件开始装载', ['rocket' => $rocket]);

        $rocket->mergePayload([
            'method' => 'alipay.ebpp.pdeduct.sign.add',
            'biz_content' => array_merge(
                [
                    'charge_inst' => 'CQCENTERELECTRIC',
                    'agent_channel' => 'PUBLICPLATFORM',
                    'deduct_prod_code' => 'INST_DIRECT_DEDUCT',
                ],
                $rocket->getParams(),
            ),
        ]);

        Logger::info('[alipay][PdeductSignAddPlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $next($rocket);
    }
}
