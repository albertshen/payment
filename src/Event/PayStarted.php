<?php

declare(strict_types=1);

namespace Albert\Payment\Event;

use Albert\Payment\Rocket;

class PayStarted extends Event
{
    /**
     * @var \Albert\Payment\Contract\PluginInterface[]
     */
    public $plugins;

    /**
     * @var array
     */
    public $params;

    public function __construct(array $plugins, array $params, ?Rocket $rocket)
    {
        $this->plugins = $plugins;
        $this->params = $params;

        parent::__construct($rocket);
    }
}
