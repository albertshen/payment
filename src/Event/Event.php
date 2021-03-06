<?php

declare(strict_types=1);

namespace Albert\Payment\Event;

use Albert\Payment\Rocket;

class Event
{
    /**
     * @var \Albert\Payment\Rocket|null
     */
    public $rocket;

    /**
     * Bootstrap.
     */
    public function __construct(?Rocket $rocket)
    {
        $this->rocket = $rocket;
    }
}
