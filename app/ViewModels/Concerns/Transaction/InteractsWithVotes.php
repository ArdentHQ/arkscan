<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Facades\Wallets;
use App\ViewModels\WalletViewModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

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
        $publicKey = collect($votes)
            ->filter(fn ($vote) => Str::startsWith($vote, '+'))
            ->first();

        return new WalletViewModel(Wallets::findByPublicKey(substr($publicKey, 1)));
    }

    public function unvoted(): ?WalletViewModel
    {
        if (! $this->isUnvote()) {
            return null;
        }

        /** @var array<int, string> */
        $votes = Arr::get($this->transaction->asset ?? [], 'votes');

        /** @var string */
        $publicKey = collect($votes)
            ->filter(fn ($vote) => Str::startsWith($vote, '-'))
            ->first();

        return new WalletViewModel(Wallets::findByPublicKey(substr($publicKey, 1)));
    }
}
