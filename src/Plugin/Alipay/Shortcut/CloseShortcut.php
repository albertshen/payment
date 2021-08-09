<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Shortcut;

use Albert\Payment\Contract\ShortcutInterface;
use Albert\Payment\Plugin\Alipay\Trade\ClosePlugin;

class CloseShortcut implements ShortcutInterface
{
    public function getPlugins(array $params): array
    {
        return [
            ClosePlugin::class,
        ];
    }
}
