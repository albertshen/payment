<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Fund\Profitsharing;

use Albert\Payment\Plugin\Wechat\GeneralPlugin;
use Albert\Payment\Rocket;

class CreatePlugin extends GeneralPlugin
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    protected function doSomething(Rocket $rocket): void
    {
        $config = get_wechat_config($rocket->getParams());

        $rocket->mergePayload([
            'appid' => $config->get('mp_app_id'),
        ]);
    }

    protected function getUri(Rocket $rocket): string
    {
        return 'v3/profitsharing/orders';
    }
}
