<?php

declare(strict_types=1);

use App\Http\Livewire\Stats\AllTimeTransactions;
use App\Models\Transaction;
use App\Services\Cache\WalletCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

beforeEach(function () {
    Carbon::setTestNow('2021-01-01 00:00:00');
});

it('should render the component', function () {
    Transaction::factory(10)->create(['timestamp' => Carbon::now()->getTimestampMs()]);
    Transaction::factory(15)->create(['timestamp' => Carbon::now()->sub('1 hour')->getTimestampMs()]);
    Transaction::factory(20)->create(['timestamp' => Carbon::now()->sub('2 hours')->getTimestampMs()]);
    Transaction::factory(25)->create(['timestamp' => Carbon::now()->sub('3 hours')->getTimestampMs()]);
    Transaction::factory(30)->create(['timestamp' => Carbon::now()->sub('4 hours')->getTimestampMs()]);
    Transaction::factory(35)->create(['timestamp' => Carbon::now()->sub('3 years')->getTimestampMs()]);

    Artisan::call('explorer:cache-transactions');

    Livewire::test(AllTimeTransactions::class)
        ->set('period', 'day')
        ->assertSee(trans('pages.statistics.information-cards.all-time-transactions'))
        ->assertSee('135')
        ->assertSee(trans('pages.statistics.information-cards.transactions'))
        ->assertSee('100')
        ->assertSee('[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,30,25,20,15,10]');
});

it('should filter by year', function () {
    Transaction::factory(10)->create(['timestamp' => Carbon::now()->getTimestampMs()]);
    Transaction::factory(15)->create(['timestamp' => Carbon::now()->sub('1 month')->getTimestampMs()]);
    Transaction::factory(20)->create(['timestamp' => Carbon::now()->sub('2 months')->getTimestampMs()]);
    Transaction::factory(25)->create(['timestamp' => Carbon::now()->sub('3 months')->getTimestampMs()]);
    Transaction::factory(30)->create(['timestamp' => Carbon::now()->sub('4 months')->getTimestampMs()]);
    Transaction::factory(35)->create(['timestamp' => Carbon::now()->sub('3 years')->getTimestampMs()]);

    Artisan::call('explorer:cache-transactions');

    Livewire::test(AllTimeTransactions::class)
        ->set('period', 'year')
        ->assertSee(trans('pages.statistics.information-cards.all-time-transactions'))
        ->assertSee('135')
        ->assertSee(trans('pages.statistics.information-cards.transactions'))
        ->assertSee('100')
        ->assertSee('[0,0,0,0,0,0,0,30,25,20,15,10]');
});

it('should throw an exception if using a wrong cache', function () {
    Transaction::factory(10)->create(['timestamp' => Carbon::now()->getTimestampMs()]);
    Transaction::factory(15)->create(['timestamp' => Carbon::now()->sub('1 month')->getTimestampMs()]);
    Transaction::factory(20)->create(['timestamp' => Carbon::now()->sub('2 months')->getTimestampMs()]);
    Transaction::factory(25)->create(['timestamp' => Carbon::now()->sub('3 months')->getTimestampMs()]);
    Transaction::factory(30)->create(['timestamp' => Carbon::now()->sub('4 months')->getTimestampMs()]);
    Transaction::factory(35)->create(['timestamp' => Carbon::now()->sub('3 years')->getTimestampMs()]);

    Artisan::call('explorer:cache-transactions');

    Livewire::test(AllTimeTransactions::class)
        ->set('cache', WalletCache::class);
})->throws(InvalidArgumentException::class, 'Given cache [App\Services\Cache\WalletCache] is invalid. Use FeeCache or TransactionCache.');
