<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Pay\Common;

use Albert\Payment\Plugin\Wechat\GeneralPlugin;
use Albert\Payment\Rocket;
use Albert\Payment\Supports\Config;

class CombinePrepayPlugin extends GeneralPlugin
{
    protected function getUri(Rocket $rocket): string
    {
        return 'v3/combine-transactions/jsapi';
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

        if (!$rocket->getPayload()->has('combine_out_trade_no')) {
            $payload['combine_out_trade_no'] = $rocket->getParams()['out_trade_no'];
        }

        $rocket->mergePayload($payload);
    }

    protected function getWechatId(Config $config): array
    {
        return [
            'combine_appid' => $config->get('combine_app_id', ''),
            'combine_mchid' => $config->get('combine_mch_id', ''),
        ];
    }
}
