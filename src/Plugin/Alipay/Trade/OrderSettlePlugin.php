<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Trade;

use Albert\Payment\Plugin\Alipay\GeneralPlugin;

class OrderSettlePlugin extends GeneralPlugin
{
    protected function getMethod(): string
    {
        return 'alipay.trade.order.settle';
    }
}
