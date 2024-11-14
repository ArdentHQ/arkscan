<?php

declare(strict_types=1);

use App\Http\Livewire\Stats\CurrentAverageFee;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

beforeEach(function () {
    Carbon::setTestNow('2021-01-01 00:00:00');
});

it('should render the component', function () {
    Transaction::factory(5)->create();

    Artisan::call('explorer:cache-fees');

    Livewire::test(CurrentAverageFee::class)
        ->set('transactionType', 'transfer')
        ->assertSee(trans('pages.statistics.information-cards.current-average-fee', ['type' => 'Transfer']))
        ->assertSee('50.2 DARK')
        ->assertSee(trans('pages.statistics.information-cards.min-fee'))
        ->assertSee('1 DARK')
        ->assertSee(trans('pages.statistics.information-cards.max-fee'))
        ->assertSee('100 DARK');
});
