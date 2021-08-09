<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Pay\Native;

use Albert\Payment\Rocket;

class PrepayPlugin extends \Albert\Payment\Plugin\Wechat\Pay\Common\PrepayPlugin
{
    protected function getUri(Rocket $rocket): string
    {
        return 'v3/pay/transactions/native';
    }
}
