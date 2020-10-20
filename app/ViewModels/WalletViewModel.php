<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Facades\Network;
use App\Models\Wallet;
use App\Services\Blockchain\NetworkStatus;
use App\Services\NumberFormatter;
use App\Services\QRCode;
use Mattiasgeniar\Percentage\Percentage;
use Spatie\ViewModels\ViewModel;

final class WalletViewModel extends ViewModel
{
    private Wallet $model;

    public function __construct(Wallet $wallet)
    {
        $this->model = $wallet;
    }

    public function address(): string
    {
        return $this->model->address;
    }

    public function balance(): string
    {
        return NumberFormatter::currency($this->model->balance / 1e8, Network::currency());
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
}
