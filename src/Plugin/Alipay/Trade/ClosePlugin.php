<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Trade;

use Albert\Payment\Plugin\Alipay\GeneralPlugin;

class ClosePlugin extends GeneralPlugin
{
    protected function getMethod(): string
    {
        return 'alipay.trade.close';
    }
}
