<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates;

use App\Enums\CoreTransactionTypeEnum;
use App\Models\Transaction;

final class LargestTransactionAggregate
{
    public function aggregate(): ?Transaction
    {
        return Transaction::whereIn('type', [CoreTransactionTypeEnum::TRANSFER, CoreTransactionTypeEnum::MULTI_PAYMENT])->orderBy('amount', 'desc')->limit(1)->first();
    }
}
