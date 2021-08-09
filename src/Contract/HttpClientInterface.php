<?php

declare(strict_types=1);

namespace Albert\Payment\Contract;

use GuzzleHttp\ClientInterface;

interface HttpClientInterface extends ClientInterface, \Psr\Http\Client\ClientInterface
{
}
