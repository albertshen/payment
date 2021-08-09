<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Pay\App;

use Albert\Payment\Rocket;
use Albert\Payment\Supports\Config;

class PrepayPlugin extends \Albert\Payment\Plugin\Wechat\Pay\Common\PrepayPlugin
{
    protected function getUri(Rocket $rocket): string
    {
        return 'v3/pay/transactions/app';
    }

    protected function getWechatId(Config $config): array
    {
        return [
            'appid' => $config->get('app_id', ''),
            'mchid' => $config->get('mch_id', ''),
        ];
    }
}
