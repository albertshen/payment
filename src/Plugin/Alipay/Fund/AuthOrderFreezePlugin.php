<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Fund;

use Closure;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Logger;
use Albert\Payment\Rocket;

class AuthOrderFreezePlugin implements PluginInterface
{
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        Logger::info('[alipay][AuthOrderFreezePlugin] 插件开始装载', ['rocket' => $rocket]);

        $rocket->mergePayload([
            'method' => 'alipay.fund.auth.order.freeze',
            'biz_content' => array_merge(
                [
                    'product_code' => 'PRE_AUTH',
                ],
                $rocket->getParams()
            ),
        ]);

        Logger::info('[alipay][AuthOrderFreezePlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $next($rocket);
    }
}
