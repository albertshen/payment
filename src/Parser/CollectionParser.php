<?php

declare(strict_types=1);

namespace Albert\Payment\Parser;

use Psr\Http\Message\ResponseInterface;
use Albert\Payment\Contract\ParserInterface;
use Albert\Payment\Pay;
use Albert\Payment\Supports\Collection;

class CollectionParser implements ParserInterface
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    public function parse(?ResponseInterface $response): Collection
    {
        return new Collection(
            Pay::get(ArrayParser::class)->parse($response)
        );
    }
}
