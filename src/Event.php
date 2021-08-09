<?php

declare(strict_types=1);

namespace Albert\Payment;

use Albert\Payment\Contract\EventDispatcherInterface;
use Albert\Payment\Exception\InvalidConfigException;

/**
 * @method static Event\Event dispatch(object $event)
 */
class Event
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     */
    public static function __callStatic(string $method, array $args): void
    {
        if (!Pay::hasContainer() || !Pay::has(EventDispatcherInterface::class)) {
            return;
        }

        $class = Pay::get(EventDispatcherInterface::class);

        if ($class instanceof \Psr\EventDispatcher\EventDispatcherInterface) {
            $class->{$method}(...$args);

            return;
        }

        throw new InvalidConfigException(InvalidConfigException::EVENT_CONFIG_ERROR);
    }
}
