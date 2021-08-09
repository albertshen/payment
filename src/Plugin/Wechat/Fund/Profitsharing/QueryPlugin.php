<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Fund\Profitsharing;

use Albert\Payment\Exception\InvalidParamsException;
use Albert\Payment\Plugin\Wechat\GeneralPlugin;
use Albert\Payment\Rocket;

class QueryPlugin extends GeneralPlugin
{
    protected function getMethod(): string
    {
        return 'GET';
    }

    protected function doSomething(Rocket $rocket): void
    {
        $rocket->setPayload(null);
    }

    /**
     * @throws \Albert\Payment\Exception\InvalidParamsException
     */
    protected function getUri(Rocket $rocket): string
    {
        $payload = $rocket->getPayload();

        if (is_null($payload->get('out_order_no')) ||
            is_null($payload->get('transaction_id'))) {
            throw new InvalidParamsException(InvalidParamsException::MISSING_NECESSARY_PARAMS);
        }

        return '/v3/profitsharing/orders/'.
            $payload->get('out_order_no').
            '?transaction_id='.$payload->get('transaction_id');
    }
}
