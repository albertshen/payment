<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Data;

use Closure;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Logger;
use Albert\Payment\Rocket;

class BillEreceiptApplyPlugin implements PluginInterface
{
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        Logger::info('[alipay][BillEreceiptApplyPlugin] 插件开始装载', ['rocket' => $rocket]);

        $rocket->mergePayload([
            'method' => 'alipay.data.bill.ereceipt.apply',
            'biz_content' => array_merge(
                [
                    'type' => 'FUND_DETAIL',
                ],
                $rocket->getParams(),
            ),
        ]);

        Logger::info('[alipay][BillEreceiptApplyPlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $next($rocket);
    }
}
