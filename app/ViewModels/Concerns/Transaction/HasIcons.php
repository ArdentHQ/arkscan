<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Services\Transactions\TransactionDirectionIcon;
use App\Services\Transactions\TransactionStateIcon;
use App\Services\Transactions\TransactionTypeIcon;

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
}
