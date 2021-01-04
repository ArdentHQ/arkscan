<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Facades\Network;
use App\Services\Cache\Concerns\ManagesCache;
use App\ViewModels\WalletViewModel;
use Closure;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class MonitorCache implements Contract
{
    use ManagesCache;

    public function setBlockCount(Closure $callback): string
    {
        return $this->remember('block_count', Network::blockTime(), $callback);
    }

    public function setTransactions(Closure $callback): int
    {
        return (int) $this->remember('transactions', Network::blockTime(), $callback);
    }

    public function setCurrentDelegate(Closure $callback): WalletViewModel
    {
        return $this->remember('current_delegate', Network::blockTime(), $callback);
    }

    public function setNextDelegate(Closure $callback): WalletViewModel
    {
        return $this->remember('next_delegate', Network::blockTime(), $callback);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('monitor');
    }
}
