<?php

declare(strict_types=1);

namespace Albert\Payment\Traits;

use ReflectionClass;
use Albert\Payment\Supports\Str;

trait Arrayable
{
    /**
     * toArray.
     */
    public function toArray(): array
    {
        $result = [];

        foreach ((new ReflectionClass($this))->getProperties() as $item) {
            $k = $item->getName();
            $method = 'get'.Str::studly($k);

            $result[Str::snake($k)] = method_exists($this, $method) ? $this->{$method}() : $this->{$k};
        }

        return $result;
    }
}
