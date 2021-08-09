<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat;

use Closure;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\ServerRequestInterface;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Exception\InvalidParamsException;
use Albert\Payment\Logger;
use Albert\Payment\Parser\NoHttpRequestParser;
use Albert\Payment\Rocket;
use Albert\Payment\Supports\Collection;

class CallbackPlugin implements PluginInterface
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Albert\Payment\Exception\InvalidResponseException
     * @throws \Albert\Payment\Exception\InvalidParamsException
     */
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        Logger::info('[wechat][CallbackPlugin] 插件开始装载', ['rocket' => $rocket]);

        $this->formatRequestAndParams($rocket);

        /* @phpstan-ignore-next-line */
        verify_wechat_sign($rocket->getDestinationOrigin(), $rocket->getParams());

        $body = json_decode($rocket->getDestination()->getBody()->getContents(), true);

        $rocket->setDirection(NoHttpRequestParser::class)->setPayload(new Collection($body));

        $body['resource'] = decrypt_wechat_resource($body['resource'] ?? [], $rocket->getParams());

        $rocket->setDestination(new Collection($body));

        Logger::info('[wechat][CallbackPlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $next($rocket);
    }

    /**
     * @throws \Albert\Payment\Exception\InvalidParamsException
     */
    protected function formatRequestAndParams(Rocket $rocket): void
    {
        $request = $rocket->getParams()['request'] ?? null;

        if (is_null($request) || !($request instanceof ServerRequestInterface)) {
            throw new InvalidParamsException(InvalidParamsException::REQUEST_NULL_ERROR);
        }

        $contents = $request->getBody()->getContents();

        $rocket->setDestination($request->withBody(Utils::streamFor($contents)))
            ->setDestinationOrigin($request->withBody(Utils::streamFor($contents)))
            ->setParams($rocket->getParams()['params'] ?? []);
    }
}
