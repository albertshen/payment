<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Pay\Combine;

use Albert\Payment\Exception\InvalidParamsException;
use Albert\Payment\Rocket;

class QueryPlugin extends \Albert\Payment\Plugin\Wechat\Pay\Common\QueryPlugin
{
    protected function getUri(Rocket $rocket): string
    {
        $payload = $rocket->getPayload();

        if (is_null($payload->get('combine_out_trade_no')) &&
            is_null($payload->get('transaction_id'))) {
            throw new InvalidParamsException(InvalidParamsException::MISSING_NECESSARY_PARAMS);
        }

        return 'v3/combine-transactions/out-trade-no/'.
            $payload->get('combine_out_trade_no', $payload->get('transaction_id'));
    }
}
