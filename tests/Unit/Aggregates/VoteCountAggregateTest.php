<?php

declare(strict_types=1);

use App\Aggregates\VoteCountAggregate;
use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Models\Transaction;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    Transaction::factory(10)->create([
        'type'       => CoreTransactionTypeEnum::VOTE,
        'type_group' => TransactionTypeGroupEnum::CORE,
    ]);

    $this->subject = new VoteCountAggregate();
});

it('should aggregate and format', function () {
    expect($this->subject->aggregate())->toBe('10');
});
