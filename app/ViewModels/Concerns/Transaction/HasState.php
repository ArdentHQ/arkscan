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
        if ($this->transaction->receipt === null) {
            return true;
        }

        return $this->transaction->receipt->status === false;
    }
}
