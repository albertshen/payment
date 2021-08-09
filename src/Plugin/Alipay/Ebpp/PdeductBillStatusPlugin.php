<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Ebpp;

use Albert\Payment\Plugin\Alipay\GeneralPlugin;

class PdeductBillStatusPlugin extends GeneralPlugin
{
    protected function getMethod(): string
    {
        return 'alipay.ebpp.pdeduct.bill.pay.status';
    }
}
