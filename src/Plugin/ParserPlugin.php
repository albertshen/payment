<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin;

use Closure;
use Albert\Payment\Contract\ParserInterface;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Exception\InvalidConfigException;
use Albert\Payment\Pay;
use Albert\Payment\Rocket;

class ParserPlugin implements PluginInterface
{
    /**
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     */
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        /* @var Rocket $rocket */
        $rocket = $next($rocket);

        /* @var \Psr\Http\Message\ResponseInterface $response */
        $response = $rocket->getDestination();

        return $rocket->setDestination(
            $this->getPacker($rocket)->parse($response)
        );
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    protected function getPacker(Rocket $rocket): ParserInterface
    {
        $packer = Pay::get($rocket->getDirection() ?? ParserInterface::class);

        $packer = is_string($packer) ? Pay::get($packer) : $packer;

        if (!($packer instanceof ParserInterface)) {
            throw new InvalidConfigException(InvalidConfigException::INVALID_PACKER);
        }

        return $packer;
    }
}
