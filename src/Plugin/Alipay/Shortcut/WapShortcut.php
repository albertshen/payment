<?php

declare(strict_types=1);

namespace Albert\Payment\Plugin\Alipay\Shortcut;

use Albert\Payment\Contract\ShortcutInterface;
use Albert\Payment\Plugin\Alipay\HtmlResponsePlugin;
use Albert\Payment\Plugin\Alipay\Trade\WapPayPlugin;

class WapShortcut implements ShortcutInterface
{
    public function getPlugins(array $params): array
    {
        return [
            WapPayPlugin::class,
            HtmlResponsePlugin::class,
        ];
    }
}
