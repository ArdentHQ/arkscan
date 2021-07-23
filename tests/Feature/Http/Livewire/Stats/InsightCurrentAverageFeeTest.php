<?php

declare(strict_types=1);

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Http\Livewire\Stats\InsightCurrentAverageFee;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

beforeEach(function () {
    Carbon::setTestNow('2021-01-01 00:00:00');
});

it('should render the component', function () {
    Transaction::factory(5)
        ->state(new Sequence(
            ['fee' => 1 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::TRANSFER],
            ['fee' => 25 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::TRANSFER],
            ['fee' => 50 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::TRANSFER],
            ['fee' => 75 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::TRANSFER],
            ['fee' => 100 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::TRANSFER],
        ))
        ->create();

    Transaction::factory(5)
        ->state(new Sequence(
            ['fee' => 2 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::MULTI_SIGNATURE],
            ['fee' => 24 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::MULTI_SIGNATURE],
            ['fee' => 50 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::MULTI_SIGNATURE],
            ['fee' => 71 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::MULTI_SIGNATURE],
            ['fee' => 99 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::MULTI_SIGNATURE],
        ))
        ->create();

    Artisan::call('explorer:cache-fees');

    Livewire::test(InsightCurrentAverageFee::class)
        ->set('transactionType', 'transfer')
        ->assertSee(trans('pages.statistics.insights.current-average-fee', ['type' => 'Transfer']))
        ->assertSee('50.2 DARK')
        ->assertSee(trans('pages.statistics.insights.min-fee'))
        ->assertSee('1 DARK')
        ->assertSee(trans('pages.statistics.insights.max-fee'))
        ->assertSee('100 DARK');
});

it('should filter by transfer', function () {
    Transaction::factory(5)
        ->state(new Sequence(
            ['fee' => 1 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::TRANSFER],
            ['fee' => 25 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::TRANSFER],
            ['fee' => 50 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::TRANSFER],
            ['fee' => 75 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::TRANSFER],
            ['fee' => 100 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::TRANSFER],
        ))
        ->create();

    Transaction::factory(5)
        ->state(new Sequence(
            ['fee' => 2 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::MULTI_SIGNATURE],
            ['fee' => 24 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::MULTI_SIGNATURE],
            ['fee' => 50 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::MULTI_SIGNATURE],
            ['fee' => 71 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::MULTI_SIGNATURE],
            ['fee' => 99 * 1e8, 'type_group' => TransactionTypeGroupEnum::CORE, 'type' => CoreTransactionTypeEnum::MULTI_SIGNATURE],
        ))
        ->create();

    Artisan::call('explorer:cache-fees');

    Livewire::test(InsightCurrentAverageFee::class)
        ->set('transactionType', 'multiSignature')
        ->assertSee(trans('pages.statistics.insights.current-average-fee', ['type' => 'Multisignature']))
        ->assertSee('49.2 DARK')
        ->assertSee(trans('pages.statistics.insights.min-fee'))
        ->assertSee('2 DARK')
        ->assertSee(trans('pages.statistics.insights.max-fee'))
        ->assertSee('99 DARK');
});
