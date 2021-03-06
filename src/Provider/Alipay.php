<?php

declare(strict_types=1);

namespace Albert\Payment\Provider;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Albert\Payment\Event;
use Albert\Payment\Pay;
use Albert\Payment\Plugin\Alipay\CallbackPlugin;
use Albert\Payment\Plugin\Alipay\LaunchPlugin;
use Albert\Payment\Plugin\Alipay\PreparePlugin;
use Albert\Payment\Plugin\Alipay\RadarPlugin;
use Albert\Payment\Plugin\Alipay\SignPlugin;
use Albert\Payment\Plugin\ParserPlugin;
use Albert\Payment\Supports\Collection;
use Albert\Payment\Supports\Str;

/**
 * @method ResponseInterface app(array $order)      APP 支付
 * @method Collection        pos(array $order)      刷卡支付
 * @method Collection        scan(array $order)     扫码支付
 * @method Collection        transfer(array $order) 帐户转账
 * @method ResponseInterface wap(array $order)      手机网站支付
 * @method ResponseInterface web(array $order)      电脑支付
 * @method Collection        mini(array $order)     小程序支付
 */
class Alipay extends AbstractProvider
{
    public const URL = [
        Pay::MODE_NORMAL => 'https://openapi.alipay.com/gateway.do?charset=utf-8',
        Pay::MODE_SANDBOX => 'https://openapi.alipaydev.com/gateway.do?charset=utf-8',
        Pay::MODE_SERVICE => 'https://openapi.alipay.com/gateway.do?charset=utf-8',
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
        $plugin = '\\Albert\\Payment\\Plugin\\Alipay\\Shortcut\\'.
            Str::studly($shortcut).'Shortcut';

        return $this->call($plugin, ...$params);
    }

    /**
     * @param string|array $order
     *
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidParamsException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    public function find($order): Collection
    {
        $order = is_array($order) ? $order : ['out_trade_no' => $order];

        Event::dispatch(new Event\MethodCalled('wechat', __METHOD__, $order, null));

        return $this->__call('query', [$order]);
    }

    /**
     * @param string|array $order
     *
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidParamsException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    public function cancel($order): Collection
    {
        $order = is_array($order) ? $order : ['out_trade_no' => $order];

        Event::dispatch(new Event\MethodCalled('wechat', __METHOD__, $order, null));

        return $this->__call('cancel', [$order]);
    }

    /**
     * @param string|array $order
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
        Event::dispatch(new Event\CallbackReceived('alipay', $contents, $params, null));

        $request = $this->getCallbackParams($contents);

        return $this->pay(
            [CallbackPlugin::class], $request->merge($params)->all()
        );
    }

    public function success(): ResponseInterface
    {
        return new Response(200, [], 'success');
    }

    public function mergeCommonPlugins(array $plugins): array
    {
        return array_merge(
            [PreparePlugin::class],
            $plugins,
            [SignPlugin::class, RadarPlugin::class],
            [LaunchPlugin::class, ParserPlugin::class],
        );
    }

    /**
     * @param array|ServerRequestInterface|null $contents
     */
    protected function getCallbackParams($contents = null): Collection
    {
        if (is_array($contents)) {
            return Collection::wrap($contents);
        }

        if ($contents instanceof ServerRequestInterface) {
            return Collection::wrap('GET' === $contents->getMethod() ? $contents->getQueryParams() :
                $contents->getParsedBody());
        }

        $request = ServerRequest::fromGlobals();

        return Collection::wrap(
            array_merge($request->getQueryParams(), $request->getParsedBody())
        );
    }
}
