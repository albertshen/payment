<?php

declare(strict_types=1);

namespace Albert\Payment\Contract;

use Psr\Http\Message\ResponseInterface;
use Albert\Payment\Supports\Collection;

interface ProviderInterface
{
    /**
     * pay.
     *
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidParamsException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     *
     * @return \Albert\Payment\Supports\Collection|\Psr\Http\Message\MessageInterface
     */
    public function pay(array $plugins, array $params);

    /**
     * Quick road - Query an order.
     *
     * @param string|array $order
     */
    public function find($order): Collection;

    /**
     * Quick road - Cancel an order.
     *
     * @param string|array $order
     */
    public function cancel($order): Collection;

    /**
     * Quick road - Close an order.
     *
     * @param string|array $order
     */
    public function close($order): Collection;

    /**
     * Quick road - Refund an order.
     */
    public function refund(array $order): Collection;

    /**
     * Verify a request.
     *
     * @param array|\Psr\Http\Message\ServerRequestInterface|null $contents
     */
    public function callback($contents = null, ?array $params = null): Collection;

    /**
     * Echo success to server.
     */
    public function success(): ResponseInterface;
}
