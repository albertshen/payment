<?php

declare(strict_types=1);

namespace Albert\Payment\Contract;

use Closure;
use Albert\Payment\Rocket;

interface PluginInterface
{
    public function assembly(Rocket $rocket, Closure $next): Rocket;
}
