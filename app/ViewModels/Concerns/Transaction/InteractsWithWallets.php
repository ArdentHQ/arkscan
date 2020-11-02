<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Facades\Wallets;
use App\ViewModels\WalletViewModel;

trait InteractsWithWallets
{
    public function sender(): ?WalletViewModel
    {
        $wallet = $this->transaction->sender;

        if (is_null($wallet)) {
            return null;
        }

        return new WalletViewModel(Wallets::findByAddress($wallet->address));
    }

    public function recipient(): ?WalletViewModel
    {
        $wallet = $this->transaction->recipient;

        if (is_null($wallet)) {
            return $this->sender();
        }

        return new WalletViewModel(Wallets::findByAddress($wallet->address));
    }
}
