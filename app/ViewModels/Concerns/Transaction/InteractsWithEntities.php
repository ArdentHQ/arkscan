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
        $transaction = $this->transaction;

        if ($this->isEntityUpdate()) {
            $transactionId = Arr::get($this->transaction, 'asset.registrationId');
            $transaction   = Transactions::findById($transactionId);
        }

        return Arr::get($transaction, 'asset.data.name');
    }

    public function entityCategory(): ?string
    {
        return null;
    }

    public function entityHash(): ?string
    {
        return Arr::get($this->transaction, 'asset.data.ipfsData');
    }
}
