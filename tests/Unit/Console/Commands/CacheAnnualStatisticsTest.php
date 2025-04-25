<?php

declare(strict_types=1);

use App\Events\Statistics\AnnualData;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\StatisticsCache;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

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
            'value'    => 10 * 1e18,
            'gas_price' => 100000000,
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

it('should not cache all years data if already cached', function () {
    $cache       = new StatisticsCache();

    // cache annual data for 2023
    $cache->setAnnualData(2023, 6, '60.0000000000000000', '0.60000000000000000000', 6);

    $this->artisan('explorer:cache-annual-statistics');

    // data for 2023 should not be changed
    expect($cache->getAnnualData(2023))->toBe([
        'year'         => 2023,
        'transactions' => 6,
        'volume'       => '60.0000000000000000',
        'fees'         => '0.60000000000000000000',
        'blocks'       => 6,
    ]);
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
            'value'    => 10 * 1e18,
            'gas_price' => 100000000,
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
            // @TODO: Amount is the sum of all the individual payments but may not
            // represent a real world scenario and should not be considered accurate
            // @see https://app.clickup.com/t/86dvf5xcm
            'value' => BigNumber::new(10 * 1e18)->plus(1 * 1e18),
        ]);

    // Current year
    Transaction::factory()
        ->count(5)
        ->withReceipt()
        ->create([
            'timestamp' => $timestamp,
            'value'    => 10 * 1e18,
            'gas_price' => 100000000,
        ]);

    Transaction::factory()
        ->multiPayment(
            $addresses,
            [BigNumber::new(10 * 1e18), BigNumber::new(1 * 1e18)]
        )
        ->withReceipt()
        ->create([
            'value'     => BigNumber::new(10 * 1e18)->plus(1 * 1e18),
            'timestamp' => $timestamp,
            'gas_price' => 100000000,
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
    $timestamp   = Timestamp::now()->getTimestampMs();

    Transaction::factory()
        ->count(5)
        ->withReceipt()
        ->create([
            'timestamp' => $timestamp,
            'value'    => 10 * 1e18,
            'gas_price' => 100000000,
        ]);

    Block::factory()->count(5)->create([
        'timestamp' => $timestamp,
    ]);

    $this->artisan('explorer:cache-annual-statistics --all');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 5,
        'volume'       => '50.0000000000000000',
        'fees'         => '0.0000105',
        'blocks'       => 5,
    ]);

    Event::assertDispatchedTimes(AnnualData::class, 1);

    Event::fake();

    $this->artisan('explorer:cache-annual-statistics --all');

    Event::assertDispatchedTimes(AnnualData::class, 0);
});

it('should dispatch event for all data when the transaction count changes', function () {
    Event::fake();

    $cache       = new StatisticsCache();
    $currentTime = Carbon::now();
    $currentYear = $currentTime->year;
    $timestamp   = Timestamp::now()->getTimestampMs();

    Transaction::factory(5)
        ->withReceipt()
        ->create([
            'timestamp' => $timestamp,
            'value'    => 10 * 1e18,
            'gas_price' => 100000000,
        ]);

    Block::factory(5)->create([
        'timestamp' => $timestamp,
    ]);

    $this->artisan('explorer:cache-annual-statistics --all');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 5,
        'volume'       => '50.0000000000000000',
        'fees'         => '0.0000105',
        'blocks'       => 5,
    ]);

    expect($cache->getAnnualData(2020))->toBeNull();

    Event::assertDispatchedTimes(AnnualData::class, 1);

    Event::fake();

    Transaction::factory()
        ->withReceipt()
        ->create([
            'block_hash'  => Block::first()->hash,
            'timestamp' => Carbon::parse('2020-03-03 01:01:01')->getTimestampMs(),
            'value'    => 10 * 1e18,
            'gas_price' => 100000000,
        ]);

    $this->artisan('explorer:cache-annual-statistics --all');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 5,
        'volume'       => '50.0000000000000000',
        'fees'         => '0.0000105',
        'blocks'       => 5,
    ]);

    expect($cache->getAnnualData(2020))->toBe([
        'year'         => 2020,
        'transactions' => 1,
        'volume'       => '10.0000000000000000',
        'fees'         => '0.0000021',
        'blocks'       => 5,
    ]);

    Event::assertDispatchedTimes(AnnualData::class, 1);
});

it('should dispatch event for specific year when the transaction count changes', function () {
    Event::fake();

    $cache       = new StatisticsCache();
    $currentTime = Carbon::now();
    $currentYear = $currentTime->year;
    $timestamp   = Timestamp::now()->getTimestampMs();

    // epoch year data previously cached
    $cache->setAnnualData(
        year: 2023,
        transactions: 3,
        volume: '50.0000000000000000',
        fees: '0.0000105',
        blocks: 3,
    );

    // Previously cached data
    $cache->setAnnualData(
        year: $currentYear,
        transactions: 5,
        volume: '50.0000000000000000',
        fees: '0.0000105',
        blocks: 5,
    );

    // since cached data points to 5 transactions it should mark as changed
    // and dispatch event if transaction count changes
    Transaction::factory(6)
        ->withReceipt()
        ->create([
            'timestamp' => $timestamp,
            'value'    => 10 * 1e18,
            'gas_price' => 100000000,
        ]);

    Block::factory(5)->create([
        'timestamp' => $timestamp,
    ]);

    $this->artisan('explorer:cache-annual-statistics');

    Event::assertDispatchedTimes(AnnualData::class, 1);
});

it('should dispatch event for all data when the block count changes', function () {
    Event::fake();

    $cache       = new StatisticsCache();
    $currentTime = Carbon::now();
    $currentYear = $currentTime->year;
    $timestamp   = Timestamp::now()->getTimestampMs();

    $blocks = Block::factory(5)->create([
        'timestamp' => $timestamp,
    ]);

    Transaction::factory(5)
        ->withReceipt()
        ->create([
            'timestamp' => $timestamp,
            'value'    => 10 * 1e18,
            'gas_price' => 100000000,
            'block_hash'  => $blocks->first()->hash,
        ]);

    $block = Block::factory()->create([
        'timestamp' => Carbon::parse('2023-03-03 00:00:00')->getTimestampMs(),
    ]);

    Transaction::factory(2)
        ->withReceipt()
        ->create([
            'timestamp' => Carbon::parse('2023-03-03 00:00:00')->getTimestampMs(),
            'value'    => 10 * 1e18,
            'gas_price' => 100000000,
            'block_hash'  => $block->hash,
        ]);

    $this->artisan('explorer:cache-annual-statistics --all');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 5,
        'volume'       => '50.0000000000000000',
        'fees'         => '0.0000105',
        'blocks'       => 5,
    ]);

    expect($cache->getAnnualData(2023))->toBe([
        'year'         => 2023,
        'transactions' => 2,
        'volume'       => '20.0000000000000000',
        'fees'         => '0.0000042',
        'blocks'       => 1,
    ]);

    Event::assertDispatchedTimes(AnnualData::class, 1);

    Event::fake();

    Block::factory()->create([
        'timestamp' => Carbon::parse('2023-05-01 00:00:00')->getTimestampMs(),
    ]);

    $this->artisan('explorer:cache-annual-statistics --all');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 5,
        'volume'       => '50.0000000000000000',
        'fees'         => '0.0000105',
        'blocks'       => 5,
    ]);

    expect($cache->getAnnualData(2023))->toBe([
        'year'         => 2023,
        'transactions' => 2,
        'volume'       => '20.0000000000000000',
        'fees'         => '0.0000042',
        'blocks'       => 2,
    ]);

    Event::assertDispatchedTimes(AnnualData::class, 1);
});

it('should dispatch event for current year when the transaction count changes', function () {
    Event::fake();

    $cache       = new StatisticsCache();
    $currentTime = Carbon::now();
    $currentYear = $currentTime->year;
    $timestamp   = Timestamp::now()->getTimestampMs();

    Transaction::factory(5)
        ->withReceipt()
        ->create([
            'timestamp' => $timestamp,
            'value'    => 10 * 1e18,
            'gas_price' => 100000000,
        ]);

    Block::factory(5)->create([
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

    Event::fake();

    Transaction::factory()
        ->withReceipt()
        ->create([
            'block_hash'  => Block::first()->hash,
            'timestamp' => Carbon::parse('2024-01-02 00:00:00')->getTimestampMs(),
            'value'    => 10 * 1e18,
            'gas_price' => 100000000,
        ]);

    $cache->setAnnualData(2017, 0, '0', '0', 0);

    $this->artisan('explorer:cache-annual-statistics');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 6,
        'volume'       => '60.0000000000000000',
        'fees'         => '0.0000126',
        'blocks'       => 5,
    ]);

    Event::assertDispatchedTimes(AnnualData::class, 1);
});

it('should dispatch event for current year when the block count changes', function () {
    Event::fake();

    $cache       = new StatisticsCache();
    $currentTime = Carbon::now();
    $currentYear = $currentTime->year;
    $timestamp   = Timestamp::now()->getTimestampMs();

    Transaction::factory(5)
        ->withReceipt()
        ->create([
            'timestamp' => $timestamp,
            'value'    => 10 * 1e18,
            'gas_price' => 100000000,
        ]);

    Block::factory(5)->create([
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

    Event::fake();

    Block::factory()->create([
        'timestamp' => Carbon::parse('2024-01-02 00:00:00')->getTimestampMs(),
    ]);

    $cache->setAnnualData(2017, 0, '0', '0', 0);

    $this->artisan('explorer:cache-annual-statistics');

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => $currentYear,
        'transactions' => 5,
        'volume'       => '50.0000000000000000',
        'fees'         => '0.0000105',
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
            'timestamp' => Carbon::parse('2017-03-21 13:00:00')->getTimestampMs(),
            'value'    => 10 * 1e18,
            'gas_price' => 100000000,
        ]);

    Block::factory()->count(6)->create([
        'timestamp' => 1,
    ]);

    $this->artisan('explorer:cache-annual-statistics');

    expect($cache->getAnnualData(2017))->toBe([
        'year'         => 2017,
        'transactions' => 6,
        'volume'       => '60.0000000000000000',
        'fees'         => '0.0000126',
        'blocks'       => 6,
    ]);
});

it('should not cache all annual data if already set', function () {
    $addresses = Wallet::factory(2)->make()->pluck('address')->toArray();

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
        ->multiPayment(
            $addresses,
            [BigNumber::new(10 * 1e18), BigNumber::new(1 * 1e18)]
        )
        ->count(3)
        ->withReceipt()
        ->create([
            // @TODO: Amount is the sum of all the individual payments but may not
            // represent a real world scenario and should not be considered accurate
            //  @see https://app.clickup.com/t/86dvf5xcm
            'value' => BigNumber::new(10 * 1e18)->plus(1 * 1e18),
        ]);

    $this->artisan('explorer:cache-annual-statistics');

    expect($cache->getAnnualData(2017))->not->toBeNull();
    expect($cache->getAnnualData(2018))->toBeNull();
    expect($cache->getAnnualData(2019))->toBeNull();
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
    $addresses = Wallet::factory(2)->make()->pluck('address')->toArray();

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
        ->multiPayment(
            $addresses,
            [BigNumber::new(10 * 1e18), BigNumber::new(1 * 1e18)]
        )
        ->count(3)
        ->withReceipt()
        ->create([
            // @TODO: Amount is the sum of all the individual payments but may not
            // represent a real world scenario and should not be considered accurate
            // @see https://app.clickup.com/t/86dvf5xcm
            'value' => BigNumber::new(10 * 1e18)->plus(1 * 1e18),
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
