<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Tools;

use Albert\Payment\Plugin\Alipay\GeneralPlugin;

class OpenAuthTokenAppPlugin extends GeneralPlugin
{
    protected function getMethod(): string
    {
        return 'alipay.open.auth.token.app';
    }
}
