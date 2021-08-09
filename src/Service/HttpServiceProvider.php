<?php

declare(strict_types=1);

namespace Albert\Payment\Service;

use GuzzleHttp\Client;
use Albert\Payment\Contract\ConfigInterface;
use Albert\Payment\Contract\HttpClientInterface;
use Albert\Payment\Contract\ServiceProviderInterface;
use Albert\Payment\Pay;

class HttpServiceProvider implements ServiceProviderInterface
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    public function register(Pay $pay, ?array $data = null): void
    {
        /* @var \Albert\Payment\Supports\Config $config */
        $config = Pay::get(ConfigInterface::class);

        $service = new Client($config->get('http', []));

        Pay::set(HttpClientInterface::class, $service);
    }
}
