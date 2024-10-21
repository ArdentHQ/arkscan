<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Actions\CacheNetworkSupply;
use App\Services\BigNumber;
use App\Services\Cache\WalletCache;
use Mattiasgeniar\Percentage\Percentage;

trait HasVoters
{
    public function votes(): float
    {
        return BigNumber::new($this->wallet->attributes['validatorVoteBalance'] ?? 0)->toFloat();
    }

    public function votesPercentage(): float
    {
        $voteBalance = (float) ($this->wallet->attributes['validatorVoteBalance'] ?? 0);

        return Percentage::calculate($voteBalance, CacheNetworkSupply::execute());
    }

    public function voterCount(): int
    {
        return (new WalletCache())->getVoterCount($this->address());
    }
}
