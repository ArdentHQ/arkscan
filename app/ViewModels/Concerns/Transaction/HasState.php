<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Actions\CacheNetworkHeight;
use App\Facades\Network;

trait HasState
{
    public function transactionError(): ?string
    {
        return $this->transaction->transactionError();
    }

    public function isConfirmed(): bool
    {
        $confirmations = CacheNetworkHeight::execute() - $this->transaction->block_number;

        return abs($confirmations) >= Network::confirmations();
    }
}
