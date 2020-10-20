<?php

declare(strict_types=1);

namespace App\Aggregates;

use App\Contracts\Aggregate;
use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Transaction;
use App\Services\NumberFormatter;

final class VoteCountAggregate implements Aggregate
{
    public function aggregate(): string
    {
        return NumberFormatter::number(Transaction::where([
            'type'       => CoreTransactionTypeEnum::VOTE,
            'type_group' => TransactionTypeGroupEnum::CORE,
        ])->count());
    }
}
