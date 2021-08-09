<?php

declare(strict_types=1);

namespace Albert\Payment\Service;

use Albert\Payment\Contract\ServiceProviderInterface;
use Albert\Payment\Pay;
use Albert\Payment\Provider\Wechat;

class WechatServiceProvider implements ServiceProviderInterface
{
    /**
     * @throws \Albert\Payment\Exception\ContainerException
     */
    public function register(Pay $pay, ?array $data = null): void
    {
        $service = function () {
            return new Wechat();
        };

        $pay::set(Wechat::class, $service);
        $pay::set('wechat', $service);
    }
}
