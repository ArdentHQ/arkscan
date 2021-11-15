<?php

declare(strict_types=1);

namespace App\Enums;

use App\Services\Cache\FeeCache;
use App\Services\Cache\TransactionCache;

final class StatsCache
{
    public const FEES = FeeCache::class;

    public const TRANSACTIONS = TransactionCache::class;
}
