<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Pay\Common;

use Closure;
use Albert\Payment\Contract\PluginInterface;
use Albert\Payment\Exception\InvalidResponseException;
use Albert\Payment\Logger;
use Albert\Payment\Rocket;
use Albert\Payment\Supports\Collection;
use Albert\Payment\Supports\Config;
use Albert\Payment\Supports\Str;

class InvokePrepayPlugin implements PluginInterface
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidResponseException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Exception
     */
    public function assembly(Rocket $rocket, Closure $next): Rocket
    {
        /* @var Rocket $rocket */
        $rocket = $next($rocket);

        Logger::info('[wechat][InvokePrepayPlugin] 插件开始装载', ['rocket' => $rocket]);

        $prepayId = $rocket->getDestination()->get('prepay_id');

        if (is_null($prepayId)) {
            Logger::error('[wechat][InvokePrepayPlugin] 预下单失败：响应缺少 prepay_id 参数', $rocket->getDestination()->all());

            throw new InvalidResponseException(InvalidResponseException::RESPONSE_MISSING_NECESSARY_PARAMS, 'Prepay Response Error: Missing PrepayId', $rocket->getDestination()->all());
        }

        $config = $this->getInvokeConfig($rocket, $prepayId);

        $rocket->setDestination($config);

        Logger::info('[wechat][InvokePrepayPlugin] 插件装载完毕', ['rocket' => $rocket]);

        return $rocket;
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\InvalidConfigException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    protected function getSign(Collection $invokeConfig, array $params): string
    {
        $contents = $invokeConfig->get('appid', $invokeConfig->get('appId'))."\n".
            $invokeConfig->get('timeStamp', '')."\n".
            $invokeConfig->get('nonceStr', '')."\n".
            $invokeConfig->get('package', '')."\n";

        return get_wechat_sign($params, $contents);
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     * @throws \Exception
     */
    protected function getInvokeConfig(Rocket $rocket, string $prepayId): Config
    {
        $config = new Config([
            'appId' => $this->getAppid($rocket),
            'timeStamp' => time().'',
            'nonceStr' => Str::random(32),
            'package' => 'prepay_id='.$prepayId,
            'signType' => 'RSA',
        ]);

        $config->set('paySign', $this->getSign($config, $rocket->getParams()));

        return $config;
    }

    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    protected function getAppid(Rocket $rocket): string
    {
        $config = get_wechat_config($rocket->getParams());

        return $config->get('mp_app_id', '');
    }
}
