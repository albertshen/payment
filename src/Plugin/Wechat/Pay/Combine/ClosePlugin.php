<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Pay\Combine;

use Albert\Payment\Exception\InvalidParamsException;
use Albert\Payment\Rocket;
use Albert\Payment\Supports\Collection;

class ClosePlugin extends \Albert\Payment\Plugin\Wechat\Pay\Common\ClosePlugin
{
    protected function getUri(Rocket $rocket): string
    {
        $payload = $rocket->getPayload();

        if (is_null($payload->get('combine_out_trade_no')) &&
            is_null($payload->get('out_trade_no'))) {
            throw new InvalidParamsException(InvalidParamsException::MISSING_NECESSARY_PARAMS);
        }

        return 'v3/combine-transactions/out-trade-no/'.
            $payload->get('combine_out_trade_no', $payload->get('out_trade_no')).
            '/close';
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    protected function doSomething(Rocket $rocket): void
    {
        $config = get_wechat_config($rocket->getParams());

        $rocket->setPayload(new Collection([
            'combine_appid' => $config->get('combine_appid', ''),
            'sub_orders' => $rocket->getParams()['sub_orders'] ?? [],
        ]));
    }
}
