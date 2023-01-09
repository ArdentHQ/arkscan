<?php

declare(strict_types=1);

namespace App\Actions;

use App\Facades\Network;
use App\Services\Cache\NetworkCache;

final class CacheNetworkSupply
{
    public static function execute(): float
    {
        return (new NetworkCache())->setSupply(function (): float {
            return Network::supply()->valueOf()->toFloat();
        });
    }
}
