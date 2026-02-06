<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\State;
use App\Services\Cache\NetworkCache;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class CacheNetworkHeight
{
    public static function execute(): int
    {
        return (new NetworkCache())->setHeight(function (): int {
            try {
                return State::latest()->block_number->toNumber();
            } catch (ModelNotFoundException) {
                return 0;
            }
        });
    }
}

