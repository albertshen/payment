<?php

declare(strict_types=1);

namespace Albert\Payment\Service;

use Albert\Payment\Contract\ConfigInterface;
use Albert\Payment\Contract\ServiceProviderInterface;
use Albert\Payment\Pay;
use Albert\Payment\Supports\Config;

class ConfigServiceProvider implements ServiceProviderInterface
{
    /**
     * @var array
     */
    private $config = [
        'logger' => [
            'enable' => false,
            'file' => null,
            'identify' => 'yansongda.pay',
            'level' => 'debug',
            'type' => 'daily',
            'max_files' => 30,
        ],
        'http' => [
            'timeout' => 5.0,
            'connect_timeout' => 3.0,
        ],
        'mode' => Pay::MODE_NORMAL,
    ];

    /**
     * @throws \Albert\Payment\Exception\ContainerException
     */
    public function register(Pay $pay, ?array $data = null): void
    {
        $config = new Config(array_replace_recursive($this->config, $data ?? []));

        Pay::set(ConfigInterface::class, $config);
        Pay::set('config', $config);
    }
}
