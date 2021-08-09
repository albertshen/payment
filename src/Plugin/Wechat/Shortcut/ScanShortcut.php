<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Wechat\Shortcut;

use Albert\Payment\Contract\ShortcutInterface;
use Albert\Payment\Plugin\Wechat\Pay\Native\PrepayPlugin;

class ScanShortcut implements ShortcutInterface
{
    public function getPlugins(array $params): array
    {
        return [
            PrepayPlugin::class,
        ];
    }
}
