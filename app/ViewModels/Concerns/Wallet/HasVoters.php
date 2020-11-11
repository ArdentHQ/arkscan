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
        return BigNumber::new($this->wallet->attributes['delegate']['voteBalance'])->toFloat();
    }

    public function votesPercentage(): float
    {
        $voteBalance = (float) $this->wallet->attributes['delegate']['voteBalance'];

        return Percentage::calculate($voteBalance, CacheNetworkSupply::execute());
    }

    public function voterCount(): int
    {
        $publicKey = $this->publicKey();

        if (is_null($publicKey)) {
            return 0;
        }

        return (new WalletCache())->getVoterCount($publicKey);
    }
}
