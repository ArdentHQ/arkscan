<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use Closure;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class DelegateCache implements Contract
{
    use ManagesCache;

    public function getTotalAmounts(): array
    {
        return $this->get('total_amounts', []);
    }

    public function setTotalAmounts(Closure $callback): array
    {
        return $this->remember('total_amounts', now()->addHour(), $callback);
    }

    public function getTotalBlocks(): array
    {
        return $this->get('total_blocks', []);
    }

    public function setTotalBlocks(Closure $callback): array
    {
        return $this->remember('total_blocks', now()->addHour(), $callback);
    }

    public function getTotalFees(): array
    {
        return $this->get('total_fees', []);
    }

    public function setTotalFees(Closure $callback): array
    {
        return $this->remember('total_fees', now()->addHour(), $callback);
    }

    public function getTotalRewards(): array
    {
        return $this->get('total_rewards', []);
    }

    public function setTotalRewards(Closure $callback): array
    {
        return $this->remember('total_rewards', now()->addHour(), $callback);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('delegate');
    }
}
