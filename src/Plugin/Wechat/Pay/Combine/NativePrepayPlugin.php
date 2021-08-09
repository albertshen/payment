<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Pay\Combine;

use Albert\Payment\Plugin\Wechat\Pay\Common\CombinePrepayPlugin;
use Albert\Payment\Rocket;

class NativePrepayPlugin extends CombinePrepayPlugin
{
    protected function getUri(Rocket $rocket): string
    {
        return 'v3/combine-transactions/native';
    }
}
