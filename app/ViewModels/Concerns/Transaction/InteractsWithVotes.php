<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Facades\Wallets;
use App\ViewModels\WalletViewModel;
use Illuminate\Support\Arr;

trait InteractsWithVotes
{
    public function voted(): ?WalletViewModel
    {
        if (! $this->isVote()) {
            return null;
        }

        /** @var array<int, string> */
        $votes = Arr::get($this->transaction->asset ?? [], 'votes');

        /** @var string */
        $publicKey = collect($votes)->firstOrFail();

        return new WalletViewModel(Wallets::findByPublicKey($publicKey));
    }

    public function unvoted(): ?WalletViewModel
    {
        if (! $this->isUnvote()) {
            return null;
        }

        /** @var array<int, string> */
        $votes = Arr::get($this->transaction->asset ?? [], 'unvotes');

        /** @var string */
        $publicKey = collect($votes)->firstOrFail();

        return new WalletViewModel(Wallets::findByPublicKey($publicKey));
    }
}
