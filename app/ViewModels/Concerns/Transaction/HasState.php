<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

trait HasState
{
    public function isConfirmed(): bool
    {
        return $this->state->isConfirmed();
    }

    public function hasFailedStatus(): bool
    {
        return $this->transaction->status === false;
    }

    public function transactionError(): ?string
    {
        return $this->transaction->transactionError();
    }
}
