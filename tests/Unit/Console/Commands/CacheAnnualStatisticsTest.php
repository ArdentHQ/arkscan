<?php

declare(strict_types=1);

use Carbon\Carbon;
use App\Models\Block;
use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\Timestamp;
use App\Events\Statistics\AnnualData;
use App\Models\Wallet;
use Illuminate\Support\Facades\Event;
use App\Services\Cache\StatisticsCache;

beforeEach(fn () => $this->travelTo(Carbon::parse('2024-05-09 15:54:00')));

it('should cache annual data for current year', function () {
    Event::fake();

    $cache       = new StatisticsCache();
    $currentTime = Carbon::now();
    $currentYear = $currentTime->year;
    $timestamp   = $currentTime->getTimestampMs();

    Transaction::factory()
        ->count(5)
        ->withReceipt()
        ->create([
            'timestamp' => $timestamp,
            'amount'    => 10 * 1e18,
            'gas_price' => 0.1,
        ]);

    Block::factory()->count(5)->create([
        'timestamp' => $timestamp,
    ]);

    $this->artisan('explorer:cache-annual-statistics');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 5,
        'volume'       => '50.0000000000000000',
        'fees'         => '0.0000105',
        'blocks'       => 5,
    ]);

    Event::assertDispatchedTimes(AnnualData::class, 1);
});

it('should cache annual data for all time', function () {
    Event::fake();

    $addresses = Wallet::factory(2)->make()->pluck('address')->toArray();

    $this->travelTo(Carbon::parse('2023-08-12'));

    $cache       = new StatisticsCache();
    $currentTime = Carbon::now();
    $currentYear = $currentTime->year;
    $timestamp   = $currentTime->getTimestampMs();

    // 2017
    $initialDate = Carbon::parse('2017-03-21 13:00:00');
    Transaction::factory()
        ->count(6)
        ->withReceipt()
        ->create([
            'timestamp' => $initialDate->getTimestampMs(),
            'amount'    => 10 * 1e18,
            'gas_price' => 0.1,
        ]);

    Block::factory()->count(6)->create([
        'timestamp' => $initialDate->getTimestampMs(),
    ]);

    // 2020, default timestamp. Needed as transaction factory will create blocks in addition
    Transaction::factory()->count(3)->create();
    Block::factory()->create();
    Transaction::factory()
        ->multiPayment(
            $addresses,
            [BigNumber::new(10 * 1e18), BigNumber::new(1 * 1e18)]
        )
        ->count(3)
        ->withReceipt()
        ->create([
            'amount' => 0,
        ]);

    // Current year
    Transaction::factory()
        ->count(5)
        ->withReceipt()
        ->create([
            'timestamp' => $timestamp,
            'amount'    => 10 * 1e18,
            'gas_price' => 0.1,
        ]);

    Transaction::factory()
        ->multiPayment(
            $addresses,
            [BigNumber::new(10 * 1e18), BigNumber::new(1 * 1e18)]
        )
        ->withReceipt()
        ->create([
            'amount' => BigNumber::new(10 * 1e18)->plus(1 * 1e18),
            'timestamp' => $timestamp,
            'gas_price' => 0.1,
        ]);

    Block::factory()->count(5)->create([
        'timestamp' => $timestamp,
    ]);

    $this->artisan('explorer:cache-annual-statistics --all');

    expect($cache->getAnnualData(2017))->toBe([
        'year'         => 2017,
        'transactions' => 6,
        'volume'       => '60.0000000000000000',
        'fees'         => '0.0000126',
        'blocks'       => 6,
    ]);

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 6,
        'volume'       => '61.0000000000000000',
        'fees'         => '0.0000126',
        'blocks'       => 5,
    ]);

    Event::assertDispatchedTimes(AnnualData::class, 1);
}); 

it('should handle null scenarios for annual data for current year', function () {
    Event::fake();

    $cache = new StatisticsCache();

    $this->artisan('explorer:cache-annual-statistics');

    expect($cache->getAnnualData(2017))->toBeNull();

    Event::assertDispatchedTimes(AnnualData::class, 0);
});

it('should handle null scenarios for annual data for all time', function () {
    Event::fake();

    $cache = new StatisticsCache();

    $this->artisan('explorer:cache-annual-statistics --all');

    expect($cache->getAnnualData(2017))->toBeNull();

    Event::assertDispatchedTimes(AnnualData::class, 0);
});

it('should not dispatch event if nothing changes', function () {
    Event::fake();

    $cache       = new StatisticsCache();
    $currentTime = Carbon::now();
    $currentYear = $currentTime->year;
    $timestamp   = Timestamp::now()->timestamp;

    Transaction::factory()
        ->count(5)
        ->withReceipt()
        ->create([
            'timestamp' => $timestamp,
            'amount'    => 10 * 1e8,
            'gas_price' => 0.1,
        ]);

    Block::factory()->count(5)->create([
        'timestamp' => $timestamp,
    ]);

    $this->artisan('explorer:cache-annual-statistics --all');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 5,
        'volume'       => '50.0000000000000000',
        'fees'         => '0.50000000000000000000',
        'blocks'       => 5,
    ]);

    Event::assertDispatchedTimes(AnnualData::class, 1);

    Event::fake();

    $this->artisan('explorer:cache-annual-statistics --all');

    Event::assertDispatchedTimes(AnnualData::class, 0);
})->only();

it('should dispatch event for all data when the transaction count changes', function () {
    Event::fake();

    $cache       = new StatisticsCache();
    $currentTime = Carbon::now();
    $currentYear = $currentTime->year;
    $timestamp   = Timestamp::now()->timestamp;

    Transaction::factory(5)
        ->withReceipt()
        ->create([
            'timestamp' => $timestamp,
            'amount'    => 10 * 1e8,
            'gas_price' => 0.1,
        ]);

    Block::factory(5)->create([
        'timestamp' => $timestamp,
    ]);

    $this->artisan('explorer:cache-annual-statistics --all');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 5,
        'volume'       => '50.0000000000000000',
        'fees'         => '0.50000000000000000000',
        'blocks'       => 5,
    ]);

    expect($cache->getAnnualData(2020))->toBeNull();

    Event::assertDispatchedTimes(AnnualData::class, 1);

    Event::fake();

    Transaction::factory()
        ->withReceipt()
        ->create([
            'block_id'  => Block::first()->id,
            'timestamp' => Timestamp::fromUnix(Carbon::parse('2020-01-01 01:01:01')->unix())->unix(),
            'amount'    => 10 * 1e8,
            'gas_price' => 0.1,
        ]);

    $this->artisan('explorer:cache-annual-statistics --all');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 5,
        'volume'       => '50.0000000000000000',
        'fees'         => '0.50000000000000000000',
        'blocks'       => 5,
    ]);

    expect($cache->getAnnualData(2020))->toBe([
        'year'         => 2020,
        'transactions' => 1,
        'volume'       => '10.0000000000000000',
        'fees'         => '0.10000000000000000000',
        'blocks'       => 5,
    ]);

    Event::assertDispatchedTimes(AnnualData::class, 1);
});

it('should dispatch event for all data when the block count changes', function () {
    Event::fake();

    $cache       = new StatisticsCache();
    $currentTime = Carbon::now();
    $currentYear = $currentTime->year;
    $timestamp   = Timestamp::now()->timestamp;

    $blocks = Block::factory(5)->create([
        'timestamp' => $timestamp,
    ]);

    Transaction::factory(5)
        ->withReceipt()
        ->create([
            'timestamp' => $timestamp,
            'amount'    => 10 * 1e8,
            'gas_price' => 0.1,
            'block_id'  => $blocks->first()->id,
        ]);

    $block = Block::factory()->create([
        'timestamp' => Timestamp::fromUnix(Carbon::parse('2023-01-01 01:01:01')->unix())->unix(),
    ]);

    Transaction::factory(2)
        ->withReceipt()
        ->create([
            'timestamp' => Timestamp::fromUnix(Carbon::parse('2023-01-01 01:01:01')->unix())->unix(),
            'amount'    => 10 * 1e8,
            'gas_price' => 0.1,
            'block_id'  => $block->id,
        ]);

    $this->artisan('explorer:cache-annual-statistics --all');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 5,
        'volume'       => '50.0000000000000000',
        'fees'         => '0.50000000000000000000',
        'blocks'       => 5,
    ]);

    expect($cache->getAnnualData(2023))->toBe([
        'year'         => 2023,
        'transactions' => 2,
        'volume'       => '20.0000000000000000',
        'fees'         => '0.20000000000000000000',
        'blocks'       => 1,
    ]);

    Event::assertDispatchedTimes(AnnualData::class, 1);

    Event::fake();

    Block::factory()->create([
        'timestamp' => Timestamp::fromUnix(Carbon::parse('2023-05-01 01:01:01')->unix())->unix(),
    ]);

    $this->artisan('explorer:cache-annual-statistics --all');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 5,
        'volume'       => '50.0000000000000000',
        'fees'         => '0.50000000000000000000',
        'blocks'       => 5,
    ]);

    expect($cache->getAnnualData(2023))->toBe([
        'year'         => 2023,
        'transactions' => 2,
        'volume'       => '20.0000000000000000',
        'fees'         => '0.20000000000000000000',
        'blocks'       => 2,
    ]);

    Event::assertDispatchedTimes(AnnualData::class, 1);
});

it('should dispatch event for current year when the transaction count changes', function () {
    Event::fake();

    $cache       = new StatisticsCache();
    $currentTime = Carbon::now();
    $currentYear = $currentTime->year;
    $timestamp   = Timestamp::now()->timestamp;

    Transaction::factory(5)
        ->withReceipt()
        ->create([
            'timestamp' => $timestamp,
            'amount'    => 10 * 1e8,
            'gas_price' => 0.1,
        ]);

    Block::factory(5)->create([
        'timestamp' => $timestamp,
    ]);

    $this->artisan('explorer:cache-annual-statistics');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 5,
        'volume'       => '50.0000000000000000',
        'fees'         => '0.50000000000000000000',
        'blocks'       => 5,
    ]);

    Event::assertDispatchedTimes(AnnualData::class, 1);

    Event::fake();

    Transaction::factory()
        ->withReceipt()
        ->create([
            'block_id'  => Block::first()->id,
            'timestamp' => Timestamp::fromUnix(Carbon::parse('2024-01-01 01:01:01')->unix())->unix(),
            'amount'    => 10 * 1e8,
            'gas_price' => 0.1,
        ]);

    $cache->setAnnualData(2017, 0, '0', '0', 0);

    $this->artisan('explorer:cache-annual-statistics');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 6,
        'volume'       => '60.0000000000000000',
        'fees'         => '0.60000000000000000000',
        'blocks'       => 5,
    ]);

    Event::assertDispatchedTimes(AnnualData::class, 1);
});

it('should dispatch event for current year when the block count changes', function () {
    Event::fake();

    $cache       = new StatisticsCache();
    $currentTime = Carbon::now();
    $currentYear = $currentTime->year;
    $timestamp   = Timestamp::now()->timestamp;

    Transaction::factory(5)
        ->withReceipt()
        ->create([
            'timestamp' => $timestamp,
            'amount'    => 10 * 1e8,
            'gas_price' => 0.1,
        ]);

    Block::factory(5)->create([
        'timestamp' => $timestamp,
    ]);

    $this->artisan('explorer:cache-annual-statistics');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 5,
        'volume'       => '50.0000000000000000',
        'fees'         => '0.50000000000000000000',
        'blocks'       => 5,
    ]);

    Event::assertDispatchedTimes(AnnualData::class, 1);

    Event::fake();

    Block::factory()->create([
        'timestamp' => Timestamp::fromUnix(Carbon::parse('2024-01-01 01:01:01')->unix())->unix(),
    ]);

    $cache->setAnnualData(2017, 0, '0', '0', 0);

    $this->artisan('explorer:cache-annual-statistics');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 5,
        'volume'       => '50.0000000000000000',
        'fees'         => '0.50000000000000000000',
        'blocks'       => 6,
    ]);

    Event::assertDispatchedTimes(AnnualData::class, 1);
});

it('should get all annual data if not already set', function () {
    $cache = new StatisticsCache();

    expect($cache->getAnnualData(2017))->toBeNull();

    // 2017
    Transaction::factory()
        ->count(6)
        ->withReceipt()
        ->create([
            'timestamp' => 1,
            'amount'    => 10 * 1e8,
            'gas_price' => 0.1,
        ]);

    Block::factory()->count(6)->create([
        'timestamp' => 1,
    ]);

    $this->artisan('explorer:cache-annual-statistics');

    expect($cache->getAnnualData(2017))->toBe([
        'year'         => 2017,
        'transactions' => 6,
        'volume'       => '60.0000000000000000',
        'fees'         => '0.60000000000000000000',
        'blocks'       => 6,
    ]);
});

it('should not cache all annual data if already set', function () {
    $cache = new StatisticsCache();

    expect($cache->getAnnualData(2017))->toBeNull();
    expect($cache->getAnnualData(2018))->toBeNull();
    expect($cache->getAnnualData(2019))->toBeNull();
    expect($cache->getAnnualData(2020))->toBeNull();
    expect($cache->getAnnualData(2021))->toBeNull();
    expect($cache->getAnnualData(2022))->toBeNull();
    expect($cache->getAnnualData(2023))->toBeNull();

    $cache->setAnnualData(2017, 6, '60.0000000000000000', '0.60000000000000000000', 6);

    expect($cache->getAnnualData(2017))->not->toBeNull();
    expect($cache->getAnnualData(2018))->toBeNull();
    expect($cache->getAnnualData(2019))->toBeNull();
    expect($cache->getAnnualData(2020))->toBeNull();
    expect($cache->getAnnualData(2021))->toBeNull();
    expect($cache->getAnnualData(2022))->toBeNull();
    expect($cache->getAnnualData(2023))->toBeNull();

    // 2020, default timestamp. Needed as transaction factory will create blocks in addition
    Transaction::factory()
        ->count(3)
        ->withReceipt()
        ->create();

    Block::factory()->create();

    Transaction::factory()
        ->multiPayment()
        ->count(3)
        ->withReceipt()
        ->create([
            'amount' => 0,
            'asset'  => ['payments' => [['amount' => 10 * 1e8, 'recipient_address' => 'Wallet1'], ['amount' => 1 * 1e8, 'recipient_address' => 'Wallet2']]],
        ]);

    $this->artisan('explorer:cache-annual-statistics');

    expect($cache->getAnnualData(2017))->not->toBeNull();
    expect($cache->getAnnualData(2018))->toBeNull();
    expect($cache->getAnnualData(2019))->toBeNull();
    expect($cache->getAnnualData(2020))->toBeNull();
    expect($cache->getAnnualData(2021))->toBeNull();
    expect($cache->getAnnualData(2022))->toBeNull();
    expect($cache->getAnnualData(2023))->toBeNull();

    expect($cache->getAnnualData(2024))->toBe([
        'year'         => 2024,
        'transactions' => 0,
        'volume'       => '0',
        'fees'         => '0',
        'blocks'       => 0,
    ]);
});

it('should cache all annual data with flag even if not already set', function () {
    $cache = new StatisticsCache();

    expect($cache->getAnnualData(2017))->toBeNull();
    expect($cache->getAnnualData(2018))->toBeNull();
    expect($cache->getAnnualData(2019))->toBeNull();
    expect($cache->getAnnualData(2020))->toBeNull();
    expect($cache->getAnnualData(2021))->toBeNull();
    expect($cache->getAnnualData(2022))->toBeNull();
    expect($cache->getAnnualData(2023))->toBeNull();

    $cache->setAnnualData(2017, 6, '60.0000000000000000', '0.60000000000000000000', 6);

    expect($cache->getAnnualData(2017))->not->toBeNull();
    expect($cache->getAnnualData(2018))->toBeNull();
    expect($cache->getAnnualData(2019))->toBeNull();
    expect($cache->getAnnualData(2020))->toBeNull();
    expect($cache->getAnnualData(2021))->toBeNull();
    expect($cache->getAnnualData(2022))->toBeNull();
    expect($cache->getAnnualData(2023))->toBeNull();

    // 2020, default timestamp. Needed as transaction factory will create blocks in addition
    Transaction::factory()
        ->count(3)
        ->withReceipt()
        ->create();

    Block::factory()->create();

    Transaction::factory()
        ->multiPayment()
        ->count(3)
        ->withReceipt()
        ->create([
            'amount' => 0,
            'asset'  => ['payments' => [['amount' => 10 * 1e8, 'recipient_address' => 'Wallet1'], ['amount' => 1 * 1e8, 'recipient_address' => 'Wallet2']]],
        ]);

    $this->artisan('explorer:cache-annual-statistics --all');

    expect($cache->getAnnualData(2017))->not->toBeNull();
    expect($cache->getAnnualData(2018))->toBeNull();
    expect($cache->getAnnualData(2019))->toBeNull();
    expect($cache->getAnnualData(2020))->not->toBeNull();
    expect($cache->getAnnualData(2021))->toBeNull();
    expect($cache->getAnnualData(2022))->toBeNull();
    expect($cache->getAnnualData(2023))->toBeNull();

    expect($cache->getAnnualData(2024))->toBe([
        'year'         => 2024,
        'transactions' => 0,
        'volume'       => '0',
        'fees'         => '0',
        'blocks'       => 0,
    ]);
});
