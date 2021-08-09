<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Shortcut;

use Albert\Payment\Contract\ShortcutInterface;
use Albert\Payment\Exception\InvalidParamsException;
use Albert\Payment\Plugin\Alipay\Fund\TransOrderQueryPlugin;
use Albert\Payment\Plugin\Alipay\Trade\FastRefundQueryPlugin;
use Albert\Payment\Plugin\Alipay\Trade\QueryPlugin;

class QueryShortcut implements ShortcutInterface
{
    /**
     * @throws \Albert\Payment\Exception\InvalidParamsException
     */
    public function getPlugins(array $params): array
    {
        $typeMethod = ($params['_type'] ?? 'default').'Plugins';

        if (isset($params['out_request_no'])) {
            return $this->refundPlugins();
        }

        if (method_exists($this, $typeMethod)) {
            return $this->{$typeMethod}();
        }

        throw new InvalidParamsException(InvalidParamsException::SHORTCUT_QUERY_TYPE_ERROR, "Query type [$typeMethod] not supported");
    }

    protected function defaultPlugins(): array
    {
        return [
            QueryPlugin::class,
        ];
    }

    protected function refundPlugins(): array
    {
        return [
            FastRefundQueryPlugin::class,
        ];
    }

    protected function transferPlugins(): array
    {
        return [
            TransOrderQueryPlugin::class,
        ];
    }
}
