<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Data;

use Albert\Payment\Plugin\Alipay\GeneralPlugin;

class BillDownloadUrlQueryPlugin extends GeneralPlugin
{
    protected function getMethod(): string
    {
        return 'alipay.data.dataservice.bill.downloadurl.query';
    }
}
