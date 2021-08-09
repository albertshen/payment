<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Pay\Common;

use Albert\Payment\Plugin\Wechat\GeneralPlugin;
use Albert\Payment\Rocket;

class GetFlowBillPlugin extends GeneralPlugin
{
    protected function getUri(Rocket $rocket): string
    {
        return 'v3/bill/fundflowbill?'.http_build_query($rocket->getParams());
    }

    protected function getMethod(): string
    {
        return 'GET';
    }

    protected function doSomething(Rocket $rocket): void
    {
        $rocket->setPayload(null);
    }
}
