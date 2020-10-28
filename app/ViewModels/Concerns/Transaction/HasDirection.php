<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

trait HasDirection
{
    public function isSent(string $address): bool
    {
        return $this->direction->isSent($address);
    }

    public function isReceived(string $address): bool
    {
        return $this->direction->isReceived($address);
    }
}
