<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

trait InteractsWithWallets
{
    public function sender(): string
    {
        $wallet = Cache::remember(
            "transaction:wallet:{$this->transaction->sender_public_key}",
            Carbon::now()->addHour(),
            fn () => $this->transaction->sender
        );

        if (is_null($wallet)) {
            return 'n/a';
        }

        return $wallet->address;
    }

    public function recipient(): string
    {
        $wallet = Cache::remember(
            "transaction:wallet:{$this->transaction->recipient_id}",
            Carbon::now()->addHour(),
            fn () => $this->transaction->recipient
        );

        if (is_null($wallet)) {
            return $this->sender();
        }

        return $wallet->address;
    }
}
