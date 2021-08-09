<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Marketing\Coupon;

use Albert\Payment\Exception\InvalidParamsException;
use Albert\Payment\Plugin\Wechat\GeneralPlugin;
use Albert\Payment\Rocket;

class QueryUserCouponsPlugin extends GeneralPlugin
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

        if (is_null($payload->get('openid'))) {
            throw new InvalidParamsException(InvalidParamsException::MISSING_NECESSARY_PARAMS);
        }

        $query = $payload->all();
        unset($query['openid']);

        return 'v3/marketing/favor/users/'.
            $payload->get('openid').
            '/coupons?'.http_build_query($query);
    }
}
