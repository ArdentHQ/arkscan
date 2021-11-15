<?php

declare(strict_types=1);

use App\Http\Livewire\Stats\InsightAllTimeTransactions;
use App\Models\Transaction;
use App\Services\Cache\WalletCache;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;

beforeEach(function () {
    Carbon::setTestNow('2021-01-01 00:00:00');
});

it('should render the component', function () {
    Transaction::factory(10)->create(['timestamp' => Timestamp::now()->unix()]);
    Transaction::factory(15)->create(['timestamp' => Timestamp::now()->sub('1 hour')->unix()]);
    Transaction::factory(20)->create(['timestamp' => Timestamp::now()->sub('2 hours')->unix()]);
    Transaction::factory(25)->create(['timestamp' => Timestamp::now()->sub('3 hours')->unix()]);
    Transaction::factory(30)->create(['timestamp' => Timestamp::now()->sub('4 hours')->unix()]);
    Transaction::factory(35)->create(['timestamp' => Timestamp::now()->sub('3 years')->unix()]);

    Artisan::call('explorer:cache-transactions');

    Livewire::test(InsightAllTimeTransactions::class)
        ->set('period', 'day')
        ->assertSee(trans('pages.statistics.insights.all-time-transactions'))
        ->assertSee('135')
        ->assertSee(trans('pages.statistics.insights.transactions'))
        ->assertSee('100')
        ->assertSee('[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,30,25,20,15,10]');
});

it('should filter by year', function () {
    Transaction::factory(10)->create(['timestamp' => Timestamp::now()->unix()]);
    Transaction::factory(15)->create(['timestamp' => Timestamp::now()->sub('1 month')->unix()]);
    Transaction::factory(20)->create(['timestamp' => Timestamp::now()->sub('2 months')->unix()]);
    Transaction::factory(25)->create(['timestamp' => Timestamp::now()->sub('3 months')->unix()]);
    Transaction::factory(30)->create(['timestamp' => Timestamp::now()->sub('4 months')->unix()]);
    Transaction::factory(35)->create(['timestamp' => Timestamp::now()->sub('3 years')->unix()]);

    Artisan::call('explorer:cache-transactions');

    Livewire::test(InsightAllTimeTransactions::class)
        ->set('period', 'year')
        ->assertSee(trans('pages.statistics.insights.all-time-transactions'))
        ->assertSee('135')
        ->assertSee(trans('pages.statistics.insights.transactions'))
        ->assertSee('100')
        ->assertSee('[0,0,0,0,0,0,0,30,25,20,15,10]');
});

it('should throw an exception if using a wrong cache', function () {
    Transaction::factory(10)->create(['timestamp' => Timestamp::now()->unix()]);
    Transaction::factory(15)->create(['timestamp' => Timestamp::now()->sub('1 month')->unix()]);
    Transaction::factory(20)->create(['timestamp' => Timestamp::now()->sub('2 months')->unix()]);
    Transaction::factory(25)->create(['timestamp' => Timestamp::now()->sub('3 months')->unix()]);
    Transaction::factory(30)->create(['timestamp' => Timestamp::now()->sub('4 months')->unix()]);
    Transaction::factory(35)->create(['timestamp' => Timestamp::now()->sub('3 years')->unix()]);

    Artisan::call('explorer:cache-transactions');

    Livewire::test(InsightAllTimeTransactions::class)
        ->set('cache', WalletCache::class);
})->throws(InvalidArgumentException::class, 'Given cache [App\Services\Cache\WalletCache] is invalid. Use FeeCache or TransactionCache.');
