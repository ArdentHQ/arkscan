<?php

declare(strict_types=1);

use App\Models\Transaction;
use App\Services\Transactions\Aggregates\Fees\Historical\AllAggregate;

it('should return float values, not BigDecimal objects', function () {
    Transaction::factory(3)->transfer()->create([
        'gas_price' => 10 * 1e18,
        'gas_used'  => 1,
    ]);

    $result = (new AllAggregate())->aggregate();

    expect($result)->not->toBeEmpty();

    $result->each(function ($value): void {
        expect($value)->toBeFloat();
    });
});

it('should return an empty collection when there are no transactions', function () {
    $result = (new AllAggregate())->aggregate();

    expect($result)->toBeEmpty();
});
