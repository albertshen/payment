<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Trade;

use Closure;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Logger;
use Albert\Payment\Rocket;
use Albert\Payment\Traits\SupportServiceProviderTrait;

class PayPlugin implements PluginInterface
{
    use SupportServiceProviderTrait;

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        Logger::info('[alipay][PayPlugin] 插件开始装载', ['rocket' => $rocket]);

        $this->loadServiceProvider($rocket);

        $rocket->mergePayload([
            'method' => 'alipay.trade.pay',
            'biz_content' => array_merge(
                [
                    'product_code' => 'FACE_TO_FACE_PAYMENT',
                    'scene' => 'bar_code',
                ],
                $rocket->getParams(),
            ),
        ]);

        Logger::info('[alipay][PayPlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $next($rocket);
    }
}
