<?php

declare(strict_types=1);

namespace Albert\Payment\Service;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Albert\Payment\Contract\EventDispatcherInterface;
use Albert\Payment\Contract\ServiceProviderInterface;
use Albert\Payment\Pay;

class EventServiceProvider implements ServiceProviderInterface
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    public function register(Pay $pay, ?array $data = null): void
    {
        if (class_exists(EventDispatcher::class)) {
            $event = Pay::get(EventDispatcher::class);

            Pay::set(EventDispatcherInterface::class, $event);
        }
    }
}
