<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Trade;

use Albert\Payment\Plugin\Alipay\GeneralPlugin;
use Albert\Payment\Rocket;
use Albert\Payment\Traits\SupportServiceProviderTrait;

class PreCreatePlugin extends GeneralPlugin
{
    use SupportServiceProviderTrait;

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    protected function doSomethingBefore(Rocket $rocket): void
    {
        $this->loadServiceProvider($rocket);
    }

    protected function getMethod(): string
    {
        return 'alipay.trade.precreate';
    }
}
