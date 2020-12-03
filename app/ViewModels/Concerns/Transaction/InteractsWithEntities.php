<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Facades\Transactions;
use App\Services\Transactions\TransactionTypeSlug;
use Illuminate\Support\Arr;

trait InteractsWithEntities
{
    public function entityType(): ?string
    {
        return (new TransactionTypeSlug($this->transaction))->exact();
    }

    public function entityName(): ?string
    {
        return $this->entityProperty('asset.data.name');
    }

    public function entityCategory(): ?string
    {
        return null;
    }

    public function entityHash(): ?string
    {
        return $this->entityProperty('asset.data.ipfsData');
    }

    private function entityProperty(string $property): ?string
    {
        $transaction = $this->transaction;

        if (($this->isEntityUpdate() || $this->isEntityResignation()) && Arr::get($transaction, $property) === null) {
            $transactionId = Arr::get($this->transaction, 'asset.registrationId');
            $transaction   = Transactions::findById($transactionId);
        }

        return Arr::get($transaction, $property);
    }
}
