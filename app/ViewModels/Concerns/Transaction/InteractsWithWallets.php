<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

trait InteractsWithWallets
{
    public function sender(): ?WalletViewModel
    {
        $wallet = $this->transaction->sender;

        if (is_null($wallet)) {
            return null;
        }

        return Cache::remember(
            "transaction:wallet:{$wallet->address}",
            Carbon::now()->addHour(),
            fn () => ViewModelFactory::make($wallet)
        );
    }

    public function recipient(): ?WalletViewModel
    {
        $wallet = $this->transaction->recipient;

        if (is_null($wallet)) {
            return $this->sender();
        }

        return Cache::remember(
            "transaction:wallet:{$wallet->address}",
            Carbon::now()->addHour(),
            fn () => ViewModelFactory::make($wallet)
        );
    }
}
