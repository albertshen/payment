<?php

declare(strict_types=1);

namespace Albert\Payment\Contract;

use Albert\Payment\Pay;

interface ServiceProviderInterface
{
    /**
     * register the service.
     */
    public function register(Pay $pay, ?array $data = null): void;
}
