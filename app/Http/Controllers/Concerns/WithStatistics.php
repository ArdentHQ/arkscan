<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Actions\CacheNetworkSupply;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\ValidatorCache;

trait WithStatistics
{
    protected function getTotalSupply(): float
    {
        return CacheNetworkSupply::execute() / config('currencies.notation.crypto', 1e18);
    }

    protected function getVotingPercent(): float
    {
        return (new NetworkCache())->getVotesPercentage();
    }

    protected function getVotingValue(): float
    {
        return (new ValidatorCache())->getTotalBalanceVoted();
    }

    protected function getWallets(): int
    {
        return Wallet::count();
    }
}
