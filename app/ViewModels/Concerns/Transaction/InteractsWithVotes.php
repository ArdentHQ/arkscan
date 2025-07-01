<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\ViewModels\WalletViewModel;

trait InteractsWithVotes
{
    public function voted(): ?WalletViewModel
    {
        if ($this->transaction->votedFor === null) {
            return null;
        }

        return new WalletViewModel($this->transaction->votedFor);
    }

    public function unvoted(): ?WalletViewModel
    {
        if ($this->transaction->unvotedFor === null) {
            return null;
        }

        return new WalletViewModel($this->transaction->unvotedFor);
    }
}
