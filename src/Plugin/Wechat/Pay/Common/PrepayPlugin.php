<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Pay\Common;

use Albert\Payment\Plugin\Wechat\GeneralPlugin;
use Albert\Payment\Rocket;
use Albert\Payment\Supports\Config;

class PrepayPlugin extends GeneralPlugin
{
    protected function getUri(Rocket $rocket): string
    {
        return 'v3/pay/transactions/jsapi';
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    protected function doSomething(Rocket $rocket): void
    {
        $config = get_wechat_config($rocket->getParams());

        $payload = $this->getWechatId($config);

        if (!$rocket->getPayload()->has('notify_url')) {
            $payload['notify_url'] = $config->get('notify_url');
        }

        $rocket->mergePayload($payload);
    }

    protected function getWechatId(Config $config): array
    {
        return [
            'appid' => $config->get('mp_app_id', ''),
            'mchid' => $config->get('mch_id', ''),
        ];
    }
}
