<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Fund;

use Albert\Payment\Plugin\Alipay\GeneralPlugin;

class TransTobankTransferPlugin extends GeneralPlugin
{
    protected function getMethod(): string
    {
        return 'alipay.fund.trans.tobank.transfer';
    }
}
