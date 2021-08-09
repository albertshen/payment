<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Fund\Profitsharing;

use Albert\Payment\Plugin\Wechat\GeneralPlugin;
use Albert\Payment\Rocket;

class ReturnPlugin extends GeneralPlugin
{
    protected function doSomething(Rocket $rocket): void
    {
    }

    protected function getUri(Rocket $rocket): string
    {
        return 'v3/profitsharing/return-orders';
    }
}
