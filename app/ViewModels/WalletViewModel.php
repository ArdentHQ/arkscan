<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Facades\Network;
use App\Models\Wallet;
use App\Services\Blockchain\NetworkStatus;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;
use App\Services\QRCode;
use App\Services\Timestamp;
use Carbon\Carbon;
use Mattiasgeniar\Percentage\Percentage;
use Spatie\ViewModels\ViewModel;

final class WalletViewModel extends ViewModel
{
    private Wallet $model;

    public function __construct(Wallet $wallet)
    {
        $this->model = $wallet;
    }

    public function url(): string
    {
        return route('wallet', $this->model->address);
    }

    public function address(): string
    {
        return $this->model->address;
    }

    public function balance(): string
    {
        return NumberFormatter::currency($this->model->balance / 1e8, Network::currency());
    }

    public function balanceFiat(): string
    {
        return ExchangeRate::convert($this->model->balance / 1e8, Timestamp::fromUnix(Carbon::now()->unix())->unix());
    }

    public function balancePercentage(): float
    {
        return Percentage::calculate($this->model->balance / 1e8, NetworkStatus::supply());
    }

    public function nonce(): string
    {
        return NumberFormatter::number($this->model->nonce);
    }

    public function votes(): string
    {
        return NumberFormatter::currency($this->model->vote_balance / 1e8, Network::currency());
    }

    public function votesPercentage(): float
    {
        return Percentage::calculate($this->model->vote_balance / 1e8, NetworkStatus::supply());
    }

    public function qrCode(): string
    {
        return QRCode::generate('ark:'.$this->model->address);
    }

    public function amountForged(): string
    {
        return NumberFormatter::currency($this->model->blocks()->sum('total_amount') / 1e8, Network::currency());
    }

    public function feesForged(): string
    {
        return NumberFormatter::currency($this->model->blocks()->sum('total_fee') / 1e8, Network::currency());
    }

    public function rewardsForged(): string
    {
        return NumberFormatter::currency($this->model->blocks()->sum('reward') / 1e8, Network::currency());
    }

    public function isKnown(): bool
    {
        return ! is_null($this->findWalletByKnown());
    }

    public function isOwnedByTeam(): bool
    {
        if (! $this->isKnown()) {
            return false;
        }

        return optional($this->findWalletByKnown())['type'] === 'team';
    }

    public function isOwnedByExchange(): bool
    {
        if (! $this->isKnown()) {
            return false;
        }

        return optional($this->findWalletByKnown())['type'] === 'exchange';
    }

    private function findWalletByKnown(): ?array
    {
        return collect(Network::knownWallets())->firstWhere('address', $this->model->address);
    }
}
