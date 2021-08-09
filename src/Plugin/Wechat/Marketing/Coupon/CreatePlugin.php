<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Marketing\Coupon;

use Albert\Payment\Plugin\Wechat\GeneralPlugin;
use Albert\Payment\Rocket;

class CreatePlugin extends GeneralPlugin
{
    protected function doSomething(Rocket $rocket): void
    {
    }

    protected function getUri(Rocket $rocket): string
    {
        return 'v3/marketing/favor/coupon-stocks';
    }
}
