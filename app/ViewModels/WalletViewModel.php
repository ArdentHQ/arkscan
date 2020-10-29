<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use App\Facades\Network;
use App\Models\Wallet;
use App\Services\Blockchain\NetworkStatus;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;
use App\Services\Timestamp;
use Mattiasgeniar\Percentage\Percentage;

final class WalletViewModel implements ViewModel
{
    use Concerns\Wallet\CanBeEntity;
    use Concerns\Wallet\CanBeDelegate;
    use Concerns\Wallet\CanForge;
    use Concerns\Wallet\CanVote;
    use Concerns\Wallet\HasType;
    use Concerns\Wallet\HasVoters;
    use Concerns\Wallet\InteractsWithMarketSquare;

    private Wallet $wallet;

    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    public function url(): string
    {
        return route('wallet', $this->wallet->address);
    }

    public function address(): string
    {
        return $this->wallet->address;
    }

    public function publicKey(): ?string
    {
        return $this->wallet->public_key;
    }

    public function balance(): string
    {
        return NumberFormatter::currency($this->wallet->balance->toFloat(), Network::currency());
    }

    public function balanceFiat(): string
    {
        return ExchangeRate::convert($this->wallet->balance->toFloat(), Timestamp::now()->unix());
    }

    public function balancePercentage(): string
    {
        return NumberFormatter::percentage(Percentage::calculate($this->wallet->balance->toFloat(), NetworkStatus::supply()));
    }

    public function nonce(): string
    {
        return NumberFormatter::number($this->wallet->nonce->toNumber());
    }
}
