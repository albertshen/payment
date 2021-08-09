<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Marketing\Coupon;

use Albert\Payment\Exception\InvalidParamsException;
use Albert\Payment\Plugin\Wechat\GeneralPlugin;
use Albert\Payment\Rocket;

class QueryCouponDetailPlugin extends GeneralPlugin
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
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidParamsException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    protected function getUri(Rocket $rocket): string
    {
        $payload = $rocket->getPayload();
        $appid = get_wechat_config($rocket->getParams())->get('mp_app_id');

        if (is_null($payload->get('coupon_id')) ||
            is_null($payload->get('openid'))) {
            throw new InvalidParamsException(InvalidParamsException::MISSING_NECESSARY_PARAMS);
        }

        return 'v3/marketing/favor/users/'.
            $payload->get('openid').
            'coupons/'.$payload->get('coupon_id').
            '?appid='.$appid;
    }
}
