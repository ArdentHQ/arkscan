<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use Closure;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class ValidatorCache implements Contract
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

    public function getTotalWalletsVoted(): int
    {
        return (int) $this->get('total_wallets_voted', 0);
    }

    public function setTotalWalletsVoted(int $count): void
    {
        $this->put('total_wallets_voted', $count);
    }

    public function getTotalBalanceVoted(): float
    {
        return (float) $this->get('total_balance_voted', 0);
    }

    public function setTotalBalanceVoted(float $balance): void
    {
        $this->put('total_balance_voted', $balance);
    }

    public function getAllVoterCounts(): array
    {
        return $this->get('voter_count_all', []);
    }

    public function setAllVoterCounts(array $count): void
    {
        $this->put('voter_count_all', $count);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('validator');
    }
}