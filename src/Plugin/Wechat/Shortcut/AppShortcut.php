<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Shortcut;

use Albert\Payment\Contract\ShortcutInterface;
use Albert\Payment\Plugin\Wechat\Pay\App\InvokePrepayPlugin;
use Albert\Payment\Plugin\Wechat\Pay\App\PrepayPlugin;

class AppShortcut implements ShortcutInterface
{
    public function getPlugins(array $params): array
    {
        return [
            PrepayPlugin::class,
            InvokePrepayPlugin::class,
        ];
    }
}
