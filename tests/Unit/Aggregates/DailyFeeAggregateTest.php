<?php

declare(strict_types=1);

use App\Aggregates\DailyFeeAggregate;
use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Transaction;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    Transaction::factory(10)->create([
        'type'       => CoreTransactionTypeEnum::DELEGATE_REGISTRATION,
        'type_group' => TransactionTypeGroupEnum::CORE,
    ]);

    $this->subject = new DailyFeeAggregate();
});

it('should aggregate and format', function () {
    expect($this->subject->aggregate())->toBeString();
});
