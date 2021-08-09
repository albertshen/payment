<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Fund;

use Albert\Payment\Plugin\Alipay\GeneralPlugin;

class TransCommonQueryPlugin extends GeneralPlugin
{
    protected function getMethod(): string
    {
        return 'alipay.fund.trans.common.query';
    }
}
