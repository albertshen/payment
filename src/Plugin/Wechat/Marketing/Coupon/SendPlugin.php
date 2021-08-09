<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Marketing\Coupon;

use Albert\Payment\Exception\InvalidParamsException;
use Albert\Payment\Plugin\Wechat\GeneralPlugin;
use Albert\Payment\Rocket;
use Albert\Payment\Supports\Collection;

class SendPlugin extends GeneralPlugin
{
    protected function doSomething(Rocket $rocket): void
    {
        $rocket->setPayload(new Collection([
            'stock_creator_mchid' => $rocket->getPayload()->get('stock_creator_mchid'),
        ]));
    }

    /**
     * @throws \Albert\Payment\Exception\InvalidParamsException
     */
    protected function getUri(Rocket $rocket): string
    {
        $payload = $rocket->getPayload();

        if (is_null($payload->get('openid'))) {
            throw new InvalidParamsException(InvalidParamsException::MISSING_NECESSARY_PARAMS);
        }

        return 'v3/marketing/favor/users/'.$payload->get('openid').'/coupons';
    }
}
