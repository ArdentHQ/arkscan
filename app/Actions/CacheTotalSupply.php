<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\State;
use App\Services\Cache\NetworkCache;

final class CacheTotalSupply
{
    public static function execute(): float
    {
        return (new NetworkCache())->setTotalSupply(function (): float {
            return (float) State::latest()->supply;
        });
    }
}
