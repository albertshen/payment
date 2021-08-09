<?php

declare(strict_types=1);

namespace Albert\Payment\Contract;

interface ShortcutInterface
{
    /**
     * @return \Albert\Payment\Contract\PluginInterface[]|string[]
     */
    public function getPlugins(array $params): array;
}
