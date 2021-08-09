<?php

declare(strict_types=1);

namespace Albert\Payment\Parser;

use Psr\Http\Message\ResponseInterface;
use Albert\Payment\Contract\ParserInterface;
use Albert\Payment\Pay;

class JsonParser implements ParserInterface
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    public function parse(?ResponseInterface $response): string
    {
        return json_encode(Pay::get(ArrayParser::class)->parse($response));
    }
}
