<?php

declare(strict_types=1);

namespace App\Aggregates;

use App\Contracts\Aggregate;
use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Transaction;

final class DelegateRegistrationCountAggregate implements Aggregate
{
    public function aggregate(): string
    {
        return (string) Transaction::where([
            'type'       => CoreTransactionTypeEnum::DELEGATE_REGISTRATION,
            'type_group' => TransactionTypeGroupEnum::CORE,
        ])->count();
    }
}
