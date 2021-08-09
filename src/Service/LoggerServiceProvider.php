<?php

declare(strict_types=1);

namespace Albert\Payment\Service;

use Albert\Payment\Contract\ConfigInterface;
use Albert\Payment\Contract\LoggerInterface;
use Albert\Payment\Contract\ServiceProviderInterface;
use Albert\Payment\Pay;
use Albert\Payment\Supports\Logger;

class LoggerServiceProvider implements ServiceProviderInterface
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    public function register(Pay $pay, ?array $data = null): void
    {
        /* @var ConfigInterface $config */
        $config = Pay::get(ConfigInterface::class);

        if (class_exists(\Monolog\Logger::class) && true === $config->get('logger.enable', false)) {
            $logger = new Logger(array_merge(
                ['identify' => 'yansongda.pay'], $config->get('logger', [])
            ));

            Pay::set(LoggerInterface::class, $logger);
        }
    }
}
