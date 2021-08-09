<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat;

use Albert\Payment\Rocket;

class WechatPublicCertsPlugin extends GeneralPlugin
{
    protected function getMethod(): string
    {
        return 'GET';
    }

    protected function doSomething(Rocket $rocket): void
    {
        $rocket->setPayload(null);
    }

    protected function getUri(Rocket $rocket): string
    {
        return 'v3/certificates';
    }
}
