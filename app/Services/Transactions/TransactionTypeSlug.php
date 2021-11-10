<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Models\Transaction;
use App\Services\Transactions\Concerns\ManagesTransactionTypes;

final class TransactionTypeSlug
{
    use ManagesTransactionTypes;

    private TransactionType $type;

    public function __construct(Transaction $transaction)
    {
        $this->type = new TransactionType($transaction);
    }

    public function generic(): string
    {
        foreach ($this->typesGeneric as $method => $slug) {
            if ((bool) call_user_func_safe([$this->type, $method])) {
                return $slug;
            }
        }

        return 'unknown';
    }

    public function exact(): string
    {
        foreach ($this->typesExact as $method => $slug) {
            if ((bool) call_user_func_safe([$this->type, $method])) {
                return $slug;
            }
        }

        return 'unknown';
    }
}
