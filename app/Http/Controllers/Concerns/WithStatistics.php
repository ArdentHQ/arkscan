<?php

namespace App\Http\Controllers\Concerns;

use App\Actions\CacheNetworkSupply;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\ValidatorCache;
use App\Services\NumberFormatter;

trait WithStatistics
{
    protected function getTotalSupply(): string
    {
        $supply = CacheNetworkSupply::execute() / config('currencies.notation.crypto', 1e18);

        return NumberFormatter::number($supply);
    }

    protected function getVotingPercent(): string
    {
        $votesPercent = (new NetworkCache())->getVotesPercentage();

        return NumberFormatter::percentage($votesPercent);
    }

    protected function getVotingValue(): float
    {
        return (new ValidatorCache())->getTotalBalanceVoted();
    }

    protected function getWallets(): string
    {
        $wallets = Wallet::count();

        return NumberFormatter::number($wallets);
    }
}
