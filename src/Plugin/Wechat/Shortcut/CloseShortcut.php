<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Shortcut;

use Albert\Payment\Contract\ShortcutInterface;
use Albert\Payment\Exception\InvalidParamsException;
use Albert\Payment\Plugin\Wechat\Pay\Common\ClosePlugin;

class CloseShortcut implements ShortcutInterface
{
    /**
     * @throws \Albert\Payment\Exception\InvalidParamsException
     */
    public function getPlugins(array $params): array
    {
        $typeMethod = ($params['_type'] ?? 'default').'Plugins';

        if (isset($params['combine_out_trade_no']) || isset($params['sub_orders'])) {
            return $this->combinePlugins();
        }

        if (method_exists($this, $typeMethod)) {
            return $this->{$typeMethod}();
        }

        throw new InvalidParamsException(InvalidParamsException::SHORTCUT_QUERY_TYPE_ERROR, "Query type [$typeMethod] not supported");
    }

    protected function defaultPlugins(): array
    {
        return [
            ClosePlugin::class,
        ];
    }

    protected function combinePlugins(): array
    {
        return [
            \Albert\Payment\Plugin\Wechat\Pay\Combine\ClosePlugin::class,
        ];
    }
}
