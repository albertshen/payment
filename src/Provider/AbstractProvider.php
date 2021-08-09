<?php

declare(strict_types=1);

namespace Albert\Payment\Provider;

use GuzzleHttp\Psr7\Utils;
use Psr\Http\Client\ClientInterface;
use Throwable;
use Albert\Payment\Contract\HttpClientInterface;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Contract\ProviderInterface;
use Albert\Payment\Contract\ShortcutInterface;
use Albert\Payment\Event;
use Albert\Payment\Exception\InvalidConfigException;
use Albert\Payment\Exception\InvalidParamsException;
use Albert\Payment\Exception\InvalidResponseException;
use Albert\Payment\Logger;
use Albert\Payment\Pay;
use Albert\Payment\Rocket;
use Albert\Payment\Supports\Collection;
use Albert\Payment\Supports\Pipeline;

abstract class AbstractProvider implements ProviderInterface
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidParamsException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     *
     * @return \Albert\Payment\Supports\Collection|\Psr\Http\Message\MessageInterface
     */
    public function call(string $plugin, array $params = [])
    {
        if (!class_exists($plugin) || !in_array(ShortcutInterface::class, class_implements($plugin))) {
            throw new InvalidParamsException(InvalidParamsException::SHORTCUT_NOT_FOUND, "[$plugin] is not incompatible");
        }

        /* @var ShortcutInterface $money */
        $money = Pay::get($plugin);

        return $this->pay(
            $this->mergeCommonPlugins($money->getPlugins($params)), $params
        );
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidParamsException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     *
     * @return \Albert\Payment\Supports\Collection|\Psr\Http\Message\MessageInterface
     */
    public function pay(array $plugins, array $params)
    {
        Logger::info('[AbstractProvider] 即将进行 pay 操作', func_get_args());

        Event::dispatch(new Event\PayStarted($plugins, $params, null));

        $this->verifyPlugin($plugins);

        /* @var Pipeline $pipeline */
        $pipeline = Pay::make(Pipeline::class);

        /* @var Rocket $rocket */
        $rocket = $pipeline
            ->send((new Rocket())->setParams($params)->setPayload(new Collection()))
            ->through($plugins)
            ->via('assembly')
            ->then(function ($rocket) {
                return $this->ignite($rocket);
            });

        Event::dispatch(new Event\PayFinish($rocket));

        return $rocket->getDestination();
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Albert\Payment\Exception\InvalidResponseException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     */
    public function ignite(Rocket $rocket): Rocket
    {
        if (!should_do_http_request($rocket)) {
            return $rocket;
        }

        /* @var HttpClientInterface $http */
        $http = Pay::get(HttpClientInterface::class);

        if (!($http instanceof ClientInterface)) {
            throw new InvalidConfigException(InvalidConfigException::HTTP_CLIENT_CONFIG_ERROR);
        }
        
        Logger::info('[AbstractProvider] 准备请求支付服务商 API', $rocket->toArray());

        Event::dispatch(new Event\ApiRequesting($rocket));

        try {
            $response = $http->sendRequest($rocket->getRadar());

            $contents = $response->getBody()->getContents();

            $rocket->setDestination($response->withBody(Utils::streamFor($contents)))
                ->setDestinationOrigin($response->withBody(Utils::streamFor($contents)));
        } catch (Throwable $e) {
            Logger::error('[AbstractProvider] 请求支付服务商 API 出错', ['message' => $e->getMessage(), 'rocket' => $rocket->toArray(), 'trace' => $e->getTrace()]);

            throw new InvalidResponseException(InvalidResponseException::REQUEST_RESPONSE_ERROR, $e->getMessage(), [], $e);
        }

        Logger::info('[AbstractProvider] 请求支付服务商 API 成功', ['response' => $response, 'rocket' => $rocket->toArray()]);

        Event::dispatch(new Event\ApiRequested($rocket));

        return $rocket;
    }

    abstract public function mergeCommonPlugins(array $plugins): array;

    /**
     * @throws \Albert\Payment\Exception\InvalidParamsException
     */
    protected function verifyPlugin(array $plugins): void
    {
        foreach ($plugins as $plugin) {
            if (is_callable($plugin)) {
                continue;
            }

            if ((is_object($plugin) ||
                    (is_string($plugin) && class_exists($plugin))) &&
                in_array(PluginInterface::class, class_implements($plugin))) {
                continue;
            }

            throw new InvalidParamsException(InvalidParamsException::PLUGIN_ERROR, "[$plugin] is not incompatible");
        }
    }
}
