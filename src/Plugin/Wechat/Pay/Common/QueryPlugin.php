<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Pay\Common;

use Albert\Payment\Exception\InvalidParamsException;
use Albert\Payment\Plugin\Wechat\GeneralPlugin;
use Albert\Payment\Rocket;

class QueryPlugin extends GeneralPlugin
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Albert\Payment\Exception\InvalidParamsException
     */
    protected function getUri(Rocket $rocket): string
    {
        $config = get_wechat_config($rocket->getParams());
        $payload = $rocket->getPayload();

        if (!is_null($payload->get('transaction_id'))) {
            return 'v3/pay/transactions/id/'.
                $payload->get('transaction_id').
                '?mchid='.$config->get('mch_id', '');
        }

        if (!is_null($payload->get('out_trade_no'))) {
            return 'v3/pay/transactions/out-trade-no/'.
                $payload->get('out_trade_no').
                '?mchid='.$config->get('mch_id', '');
        }

        throw new InvalidParamsException(InvalidParamsException::MISSING_NECESSARY_PARAMS);
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
