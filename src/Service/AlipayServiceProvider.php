<?php

declare(strict_types=1);

namespace Albert\Payment\Service;

use Albert\Payment\Contract\ParserInterface;
use Albert\Payment\Contract\ServiceProviderInterface;
use Albert\Payment\Parser\CollectionParser;
use Albert\Payment\Pay;
use Albert\Payment\Provider\Alipay;

class AlipayServiceProvider implements ServiceProviderInterface
{
    /**
     * @throws \Albert\Payment\Exception\ContainerException
     */
    public function register(Pay $pay, ?array $data = null): void
    {
        Pay::set(ParserInterface::class, CollectionParser::class);

        $service = function () {
            return new Alipay();
        };

        $pay::set(Alipay::class, $service);
        $pay::set('alipay', $service);
    }
}
