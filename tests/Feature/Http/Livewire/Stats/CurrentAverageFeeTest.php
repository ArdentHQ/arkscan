<?php

declare(strict_types=1);

use App\Http\Livewire\Stats\CurrentAverageFee;
use App\Models\Receipt;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

beforeEach(function () {
    Carbon::setTestNow('2021-01-01 00:00:00');
});

it('should render the component', function () {
    Transaction::factory()->create([
        'amount'    => 0,
        'gas_price' => 100,
    ]);

    Transaction::factory()->create([
        'amount'    => 0,
        'gas_price' => 1,
    ]);

    Transaction::factory(5)->create([
        'gas_price' => 50.2,
    ]);

    foreach (Transaction::all() as $transaction) {
        Receipt::factory()->create([
            'id'       => $transaction->id,
            'gas_used' => 1e9,
        ]);
    }

    Artisan::call('explorer:cache-fees');

    Livewire::test(CurrentAverageFee::class)
        ->set('transactionType', 'transfer')
        ->assertSee(trans('pages.statistics.information-cards.current-average-fee', ['type' => 'Transfer']))
        ->assertSee('50.28571429 DARK')
        ->assertSee(trans('pages.statistics.information-cards.min-fee'))
        ->assertSee('1 DARK')
        ->assertSee(trans('pages.statistics.information-cards.max-fee'))
        ->assertSee('100 DARK');
});
