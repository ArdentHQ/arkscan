<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Services\Transactions\TransactionDirectionIcon;
use App\Services\Transactions\TransactionStateIcon;
use App\Services\Transactions\TransactionTypeIcon;
use App\Services\Transactions\TransactionTypeSlug;

trait HasIcons
{
    public function iconState(): string
    {
        return (new TransactionStateIcon($this->transaction))->name();
    }

    public function iconType(): string
    {
        return (new TransactionTypeIcon($this->transaction))->name();
    }

    public function iconDirection(string $address): string
    {
        return (new TransactionDirectionIcon($this->transaction))->name($address);
    }

    public function typeSlug(): string
    {
        return (new TransactionTypeSlug($this->transaction))->generic();
    }

    public function componentSlug(): string
    {
        return (new TransactionTypeSlug($this->transaction))->generic();
    }
}
