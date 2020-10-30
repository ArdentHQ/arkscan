<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Blockchain\NetworkStatus;
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

        return BigNumber::new(Percentage::calculate($voteBalance, NetworkStatus::supply()))->toFloat();
    }

    public function voterCount(): int
    {
        return Wallet::where('attributes->vote', $this->publicKey())->count();
    }
}
