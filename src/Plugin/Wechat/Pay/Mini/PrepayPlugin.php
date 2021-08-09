<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Pay\Mini;

use Albert\Payment\Supports\Config;

class PrepayPlugin extends \Albert\Payment\Plugin\Wechat\Pay\Common\PrepayPlugin
{
    protected function getWechatId(Config $config): array
    {
        return [
            'appid' => $config->get('mini_app_id', ''),
            'mchid' => $config->get('mch_id', ''),
        ];
    }
}
