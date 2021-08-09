<?php

declare(strict_types=1);

namespace Albert\Payment\Parser;

use Psr\Http\Message\ResponseInterface;
use Albert\Payment\Contract\ParserInterface;
use Albert\Payment\Exception\InvalidResponseException;

class ArrayParser implements ParserInterface
{
    /**
     * @throws \Albert\Payment\Exception\InvalidResponseException
     */
    public function parse(?ResponseInterface $response): array
    {
        if (is_null($response)) {
            throw new InvalidResponseException(InvalidResponseException::RESPONSE_NONE);
        }

        $contents = $response->getBody()->getContents();

        $result = json_decode($contents, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidResponseException(InvalidResponseException::UNPACK_RESPONSE_ERROR, 'Unpack Response Error', ['contents' => $contents, 'response' => $response]);
        }

        return $result;
    }
}
