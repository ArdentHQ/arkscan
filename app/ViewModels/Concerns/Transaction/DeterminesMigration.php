<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

trait DeterminesMigration
{
    public function isMigration(): bool
    {
        if (! $this->isTransfer()) {
            return false;
        }

        if ($this->transaction->recipient?->address !== config('explorer.migration.address')) {
            return false;
        }

        if ($this->transaction->amount->valueOf()->toFloat() < config('explorer.migration.minimum_amount')) {
            return false;
        }

        if ($this->transaction->fee->valueOf()->toFloat() < config('explorer.migration.minimum_fee')) {
            return false;
        }

        return $this->migratedAddress() !== null;
    }
}
