<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Pay\Common;

use Albert\Payment\Exception\InvalidParamsException;
use Albert\Payment\Parser\OriginResponseParser;
use Albert\Payment\Plugin\Wechat\GeneralPlugin;
use Albert\Payment\Rocket;
use Albert\Payment\Supports\Collection;

class ClosePlugin extends GeneralPlugin
{
    /**
     * @throws \Albert\Payment\Exception\InvalidParamsException
     */
    protected function getUri(Rocket $rocket): string
    {
        $payload = $rocket->getPayload();

        if (is_null($payload->get('out_trade_no'))) {
            throw new InvalidParamsException(InvalidParamsException::MISSING_NECESSARY_PARAMS);
        }

        return 'v3/pay/transactions/out-trade-no/'.
            $payload->get('out_trade_no').
            '/close';
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    protected function doSomething(Rocket $rocket): void
    {
        $rocket->setDirection(OriginResponseParser::class);

        $config = get_wechat_config($rocket->getParams());

        $rocket->setPayload(new Collection([
            'mchid' => $config->get('mch_id', ''),
        ]));
    }
}
