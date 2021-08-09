<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Shortcut;

use Albert\Payment\Contract\ShortcutInterface;
use Albert\Payment\Plugin\Alipay\Trade\CreatePlugin;

class MiniShortcut implements ShortcutInterface
{
    public function getPlugins(array $params): array
    {
        return [
            CreatePlugin::class,
        ];
    }
}
