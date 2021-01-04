<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Facades\Network;
use App\Services\Cache\Concerns\ManagesCache;
use Closure;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class TableCache implements Contract
{
    use ManagesCache;

    public function setLatestBlocks(Closure $callback): Collection
    {
        return $this->remember('latest_blocks', Network::blockTime(), $callback);
    }

    public function setLatestTransactions(string $type, Closure $callback): Collection
    {
        return $this->remember("latest_transactions/$type", Network::blockTime(), $callback);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('table');
    }
}
