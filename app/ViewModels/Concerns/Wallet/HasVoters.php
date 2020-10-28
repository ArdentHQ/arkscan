<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Facades\Network;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Blockchain\NetworkStatus;
use App\Services\NumberFormatter;
use Mattiasgeniar\Percentage\Percentage;

trait HasVoters
{
    public function votes(): string
    {
        return NumberFormatter::currency(
            BigNumber::new($this->wallet->attributes['delegate']['voteBalance'])->toFloat(),
            Network::currency()
        );
    }

    public function votesPercentage(): string
    {
        $voteBalance = (float) $this->wallet->attributes['delegate']['voteBalance'];

        return NumberFormatter::percentage(BigNumber::new(Percentage::calculate($voteBalance, NetworkStatus::supply()))->toFloat());
    }

    public function voterCount(): string
    {
        return NumberFormatter::number(Wallet::where('attributes->vote', $this->publicKey())->count());
    }
}
