<?php

declare(strict_types=1);

namespace Albert\Payment\Traits;

use Albert\Payment\Pay;
use Albert\Payment\Rocket;

trait SupportServiceProviderTrait
{
    /**
     * @throws \Albert\Payment\Exception\ContainerDependencyException
     * @throws \Albert\Payment\Exception\ContainerException
     * @throws \Albert\Payment\Exception\ServiceNotFoundException
     */
    protected function loadServiceProvider(Rocket $rocket): void
    {
        $params = $rocket->getParams();
        $config = get_alipay_config($params);
        $serviceProviderId = $config->get('service_provider_id');

        if (Pay::MODE_SERVICE !== $config->get('mode', Pay::MODE_NORMAL) ||
            empty($serviceProviderId)) {
            return;
        }

        $rocket->mergeParams([
            'extend_params' => array_merge($params['extend_params'] ?? [], ['sys_service_provider_id' => $serviceProviderId]),
        ]);
    }
}
