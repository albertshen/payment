<?php

declare(strict_types=1);

namespace Albert\Payment\Provider;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Albert\Payment\Event;
use Albert\Payment\Exception\InvalidParamsException;
use Albert\Payment\Pay;
use Albert\Payment\Plugin\ParserPlugin;
use Albert\Payment\Plugin\Wechat\CallbackPlugin;
use Albert\Payment\Plugin\Wechat\LaunchPlugin;
use Albert\Payment\Plugin\Wechat\PreparePlugin;
use Albert\Payment\Plugin\Wechat\SignPlugin;
use Albert\Payment\Supports\Collection;
use Albert\Payment\Supports\Str;

/**
 * @method ResponseInterface app(array $order)  APP 支付
 * @method Collection        mini(array $order) 小程序支付
 * @method Collection        mp(array $order)   公众号支付
 * @method Collection        scan(array $order) 扫码支付
 * @method ResponseInterface wap(array $order)  H5 支付
 */
class Wechat extends AbstractProvider
{
    public const AUTH_TAG_LENGTH_BYTE = 16;

    public const MCH_SECRET_KEY_LENGTH_BYTE = 32;

    public const URL = [
        Pay::MODE_NORMAL => 'https://api.mch.weixin.qq.com/',
        Pay::MODE_SANDBOX => 'https://api.mch.weixin.qq.com/sandboxnew/',
        Pay::MODE_SERVICE => 'https://api.mch.weixin.qq.com/',
    ];

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidParamsException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     *
     * @return \Albert\Payment\Supports\Collection|\Psr\Http\Message\MessageInterface
     */
    public function __call(string $shortcut, array $params)
    {
        $plugin = '\\Albert\\Payment\\Plugin\\Wechat\\Shortcut\\'.
            Str::studly($shortcut).'Shortcut';

        return $this->call($plugin, ...$params);
    }

    /**
     * @param array|string $order
     *
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidParamsException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    public function find($order): Collection
    {
        $order = is_array($order) ? $order : ['transaction_id' => $order];

        Event::dispatch(new Event\MethodCalled('wechat', __METHOD__, $order, null));

        return $this->__call('query', [$order]);
    }

    /**
     * @param array|string $order
     *
     * @throws \Albert\Payment\Exception\InvalidParamsException
     */
    public function cancel($order): Collection
    {
        throw new InvalidParamsException(InvalidParamsException::METHOD_NOT_SUPPORTED, 'Wechat does not support cancel api');
    }

    /**
     * @param array|string $order
     *
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidParamsException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    public function close($order): Collection
    {
        $order = is_array($order) ? $order : ['out_trade_no' => $order];

        Event::dispatch(new Event\MethodCalled('wechat', __METHOD__, $order, null));

        return $this->__call('close', [$order]);
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidParamsException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    public function refund(array $order): Collection
    {
        Event::dispatch(new Event\MethodCalled('wechat', __METHOD__, $order, null));

        return $this->__call('refund', [$order]);
    }

    /**
     * @param array|\Psr\Http\Message\ServerRequestInterface|null $contents
     *
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidParamsException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    public function callback($contents = null, ?array $params = null): Collection
    {
        Event::dispatch(new Event\CallbackReceived('wechat', $contents, $params, null));

        $request = $this->getCallbackParams($contents);

        return $this->pay(
            [CallbackPlugin::class], ['request' => $request, 'params' => $params]
        );
    }

    public function success(): ResponseInterface
    {
        return new Response(
            200,
            ['Content-Type' => 'application/json'],
            json_encode(['code' => 'SUCCESS', 'message' => '成功']),
        );
    }

    public function mergeCommonPlugins(array $plugins): array
    {
        return array_merge(
            [PreparePlugin::class],
            $plugins,
            [SignPlugin::class],
            [LaunchPlugin::class, ParserPlugin::class],
        );
    }

    /**
     * @param array|ServerRequestInterface|null $contents
     */
    protected function getCallbackParams($contents = null): ServerRequestInterface
    {
        if (is_array($contents) && isset($contents['body']) && isset($contents['headers'])) {
            return new ServerRequest('POST', 'http://localhost', $contents['headers'], $contents['body']);
        }

        if (is_array($contents)) {
            return new ServerRequest('POST', 'http://localhost', [], json_encode($contents));
        }

        if ($contents instanceof ServerRequestInterface) {
            return $contents;
        }

        return ServerRequest::fromGlobals();
    }
}
