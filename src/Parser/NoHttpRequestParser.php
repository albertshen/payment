<?php

declare(strict_types=1);

namespace Albert\Payment\Parser;

use Psr\Http\Message\ResponseInterface;
use Albert\Payment\Contract\ParserInterface;

class NoHttpRequestParser implements ParserInterface
{
    public function parse(?ResponseInterface $response): ?ResponseInterface
    {
        return $response;
    }
}
