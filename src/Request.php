<?php

declare(strict_types=1);

namespace Albert\Payment;

use JsonSerializable as JsonSerializableInterface;
use Serializable as SerializableInterface;
use Albert\Payment\Traits\Accessable;
use Albert\Payment\Traits\Arrayable;
use Albert\Payment\Traits\Serializable;

class Request extends \GuzzleHttp\Psr7\Request implements JsonSerializableInterface, SerializableInterface
{
    use Accessable;
    use Arrayable;
    use Serializable;

    public function toArray(): array
    {
        return [
            'url' => $this->getUri()->__toString(),
            'method' => $this->getMethod(),
            'headers' => $this->getHeaders(),
            'body' => $this->getBody()->getContents(),
        ];
    }
}
