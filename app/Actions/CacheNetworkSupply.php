<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Wallet;
use App\Services\Cache\NetworkCache;

final class CacheNetworkSupply
{
    public static function execute(): float
    {
        return (new NetworkCache())->setSupply(function (): float {
            return (float) Wallet::where('balance', '>', 0)->sum('balance');
        });
    }
}
