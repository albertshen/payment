<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Data;

use Albert\Payment\Plugin\Alipay\GeneralPlugin;

class BillEreceiptQueryPlugin extends GeneralPlugin
{
    protected function getMethod(): string
    {
        return 'alipay.data.bill.ereceipt.query';
    }
}
