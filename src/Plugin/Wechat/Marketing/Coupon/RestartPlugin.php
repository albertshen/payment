<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Marketing\Coupon;

use Albert\Payment\Exception\InvalidParamsException;
use Albert\Payment\Plugin\Wechat\GeneralPlugin;
use Albert\Payment\Rocket;
use Albert\Payment\Supports\Collection;

class RestartPlugin extends GeneralPlugin
{
    /**
     * @throws \Albert\Payment\Exception\InvalidParamsException
     */
    protected function doSomething(Rocket $rocket): void
    {
        $payload = $rocket->getPayload();

        if (is_null($payload->get('stock_creator_mchid'))) {
            throw new InvalidParamsException(InvalidParamsException::MISSING_NECESSARY_PARAMS);
        }

        $rocket->setPayload(new Collection([
            'stock_creator_mchid' => $payload->get('stock_creator_mchid'),
        ]));
    }

    /**
     * @throws \Albert\Payment\Exception\InvalidParamsException
     */
    protected function getUri(Rocket $rocket): string
    {
        $payload = $rocket->getPayload();

        if (is_null($payload->get('stock_id'))) {
            throw new InvalidParamsException(InvalidParamsException::MISSING_NECESSARY_PARAMS);
        }

        return 'v3/marketing/favor/stocks/'.$payload->get('stock_id').'/restart';
    }
}
