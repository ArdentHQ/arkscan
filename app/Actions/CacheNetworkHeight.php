<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Block;
use App\Services\Cache\NetworkCache;

final class CacheNetworkHeight
{
    public static function execute(): int
    {
        return (new NetworkCache())->setHeight(fn (): int => Block::max('height') ?? 0);
    }
}
