<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Facades\Network;
use App\Models\Wallet;
use App\Services\NumberFormatter;
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

    public function nonce(): string
    {
        return NumberFormatter::number($this->model->nonce);
    }

    public function voteBalance(): string
    {
        return NumberFormatter::currency($this->model->vote_balance / 1e8, Network::currency());
    }
}
