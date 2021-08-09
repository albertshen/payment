<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Pay\App;

use Albert\Payment\Rocket;
use Albert\Payment\Supports\Config;
use Albert\Payment\Supports\Str;

class InvokePrepayPlugin extends \Albert\Payment\Plugin\Wechat\Pay\Common\InvokePrepayPlugin
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Exception
     */
    protected function getInvokeConfig(Rocket $rocket, string $prepayId): Config
    {
        $config = new Config([
            'appid' => $this->getAppid($rocket),
            'partnerid' => get_wechat_config($rocket->getParams())->get('mch_id'),
            'prepayid' => $prepayId,
            'package' => 'Sign=WXPay',
            'noncestr' => Str::random(32),
            'timestamp' => time().'',
        ]);

        $config->set('sign', $this->getSign($config, $rocket->getParams()));

        return $config;
    }

    protected function getAppid(Rocket $rocket): string
    {
        $config = get_wechat_config($rocket->getParams());

        return $config->get('app_id', '');
    }
}
