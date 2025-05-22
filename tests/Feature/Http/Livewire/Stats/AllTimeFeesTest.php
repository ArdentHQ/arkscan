<?php

declare(strict_types=1);

use App\Enums\StatsPeriods;
use App\Http\Livewire\Stats\AllTimeFees;
use App\Models\Receipt;
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
    Transaction::factory(10)->create(['gas_price' => 123456789, 'timestamp' => Timestamp::now()->getTimestampMs()]);
    Transaction::factory(15)->create(['gas_price' => 123456789, 'timestamp' => Timestamp::now()->sub('1 hour')->getTimestampMs()]);
    Transaction::factory(20)->create(['gas_price' => 123456789, 'timestamp' => Timestamp::now()->sub('2 hours')->getTimestampMs()]);
    Transaction::factory(25)->create(['gas_price' => 123456789, 'timestamp' => Timestamp::now()->sub('3 hours')->getTimestampMs()]);
    Transaction::factory(30)->create(['gas_price' => 123456789, 'timestamp' => Timestamp::now()->sub('4 hours')->getTimestampMs()]);
    Transaction::factory(35)->create(['gas_price' => 123456789, 'timestamp' => Timestamp::now()->sub('25 hours')->getTimestampMs()]);

    foreach (Transaction::all() as $transaction) {
        Receipt::factory()->create([
            'transaction_hash' => $transaction->hash,
            'gas_used'         => 10000,
        ]);
    }

    Artisan::call('explorer:cache-fees');

    Livewire::test(AllTimeFees::class)
        ->set('period', StatsPeriods::DAY)
        ->assertSee(trans('pages.statistics.information-cards.all-time-fees-collected'))
        ->assertSee('0.00016667 DARK')
        ->assertSee(trans('pages.statistics.information-cards.fees'))
        ->assertSee('0.00012346 DARK')
        ->assertSee('[0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,37037036700000,30864197250000,24691357800000,18518518350000,12345678900000]');
});

it('should filter by year', function () {
    Transaction::factory(10)->create(['gas_price' => 123456789, 'timestamp' => Timestamp::now()->getTimestampMs()]);
    Transaction::factory(15)->create(['gas_price' => 123456789, 'timestamp' => Timestamp::now()->sub('1 month')->getTimestampMs()]);
    Transaction::factory(20)->create(['gas_price' => 123456789, 'timestamp' => Timestamp::now()->sub('2 months')->getTimestampMs()]);
    Transaction::factory(25)->create(['gas_price' => 123456789, 'timestamp' => Timestamp::now()->sub('3 months')->getTimestampMs()]);
    Transaction::factory(30)->create(['gas_price' => 123456789, 'timestamp' => Timestamp::now()->sub('4 months')->getTimestampMs()]);
    Transaction::factory(35)->create(['gas_price' => 123456789, 'timestamp' => Timestamp::now()->sub('13 months')->getTimestampMs()]);

    foreach (Transaction::all() as $transaction) {
        Receipt::factory()->create([
            'transaction_hash' => $transaction->hash,
            'gas_used'         => 10000,
        ]);
    }

    $total  = 10 * (10 * 1e8);
    $total += 15 * (10 * 1e8);
    $total += 20 * (10 * 1e8);
    $total += 25 * (10 * 1e8);
    $total += 30 * (10 * 1e8);
    $total += 35 * (10 * 1e8);

    Artisan::call('explorer:cache-fees');

    Livewire::test(AllTimeFees::class)
        ->set('period', StatsPeriods::YEAR)
        ->assertSee(trans('pages.statistics.information-cards.all-time-fees-collected'))
        ->assertSee('0.00016667 DARK')
        ->assertSee(trans('pages.statistics.information-cards.fees'))
        ->assertSee('0.00012346 DARK')
        ->assertSee('[0,0,0,0,0,0,0,37037036700000,30864197250000,24691357800000,18518518350000,12345678900000]');
});

it('should throw an exception if using a wrong cache', function () {
    Transaction::factory(10)->create(['gas_price' => 123.45678910, 'timestamp' => Timestamp::now()->getTimestampMs()]);
    Transaction::factory(15)->create(['gas_price' => 123.45678910, 'timestamp' => Timestamp::now()->sub('1 month')->getTimestampMs()]);
    Transaction::factory(20)->create(['gas_price' => 123.45678910, 'timestamp' => Timestamp::now()->sub('2 months')->getTimestampMs()]);
    Transaction::factory(25)->create(['gas_price' => 123.45678910, 'timestamp' => Timestamp::now()->sub('3 months')->getTimestampMs()]);
    Transaction::factory(30)->create(['gas_price' => 123.45678910, 'timestamp' => Timestamp::now()->sub('4 months')->getTimestampMs()]);
    Transaction::factory(35)->create(['gas_price' => 123.45678910, 'timestamp' => Timestamp::now()->sub('13 months')->getTimestampMs()]);

    foreach (Transaction::all() as $transaction) {
        Receipt::factory()->create([
            'transaction_hash'       => $transaction->hash,
            'gas_used'               => 1e18,
        ]);
    }

    Artisan::call('explorer:cache-fees');

    Livewire::test(AllTimeFees::class)
        ->set('cache', WalletCache::class);
})->throws(InvalidArgumentException::class, 'Given cache [App\Services\Cache\WalletCache] is invalid. Use FeeCache or TransactionCache.');
