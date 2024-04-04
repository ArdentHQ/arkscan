<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Services\Cache\StatisticsCache;
use Carbon\Carbon;

it('should cache annual data for current year', function () {
    $cache       = new StatisticsCache();
    $currentTime = Carbon::now();
    $currentYear = $currentTime->year;
    $timestamp   = $currentTime->getTimestampMs();

    Transaction::factory()->count(5)->create([
        'timestamp' => $timestamp,
        'amount'    => 10 * 1e8,
        'fee'       => 0.1 * 1e8,
    ]);
    Block::factory()->count(5)->create([
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
});

it('should cache annual data for all time', function () {
    $this->travelTo(Carbon::parse('2023-08-12'));

    $cache       = new StatisticsCache();
    $currentTime = Carbon::now();
    $currentYear = $currentTime->year;
    $timestamp   = $currentTime->getTimestampMs();

    // 2017
    $initialDate = Carbon::parse('2017-03-21 13:00:00');
    Transaction::factory()->count(6)->create([
        'timestamp' => $initialDate->getTimestampMs(),
        'amount'    => 10 * 1e8,
        'fee'       => 0.1 * 1e8,
    ]);
    Block::factory()->count(6)->create([
        'timestamp' => $initialDate->getTimestampMs(),
    ]);

    // 2020, default timestamp. Needed as transaction factory will create blocks in addition
    Transaction::factory()->count(3)->create();
    Block::factory()->create();
    Transaction::factory()->multiPayment()->count(3)->create([
        'amount' => 0,
        'asset'  => ['payments' => [['amount' => 10 * 1e8, 'recipientId' => 'Wallet1'], ['amount' => 1 * 1e8, 'recipientId' => 'Wallet2']]],
    ]);

    // Current year
    Transaction::factory()->count(5)->create([
        'timestamp' => $timestamp,
        'amount'    => 10 * 1e8,
        'fee'       => 0.1 * 1e8,
    ]);
    Transaction::factory()->multiPayment()->create([
        'timestamp' => $timestamp,
        'amount'    => 0,
        'asset'     => ['payments' => [['amount' => 10 * 1e8, 'recipientId' => 'Wallet1'], ['amount' => 1 * 1e8, 'recipientId' => 'Wallet2']]],
        'fee'       => 0.1 * 1e8,
    ]);
    Block::factory()->count(5)->create([
        'timestamp' => $timestamp,
    ]);

    $this->artisan('explorer:cache-annual-statistics --all');

    expect($cache->getAnnualData(2017))->toBe([
        'year'         => 2017,
        'transactions' => 6,
        'volume'       => '60.0000000000000000',
        'fees'         => '0.60000000000000000000',
        'blocks'       => 6,
    ]);

    expect($cache->getAnnualData($currentYear))->toBe([
        'year'         => 2023,
        'transactions' => 6,
        'volume'       => '61.0000000000000000',
        'fees'         => '0.60000000000000000000',
        'blocks'       => 5,
    ]);
});

it('should handle null scenarios for annual data for current year', function () {
    $cache = new StatisticsCache();

    $this->artisan('explorer:cache-annual-statistics');

    expect($cache->getAnnualData(2017))->toBeNull();
});

it('should handle null scenarios for annual data for all time', function () {
    $cache = new StatisticsCache();

    $this->artisan('explorer:cache-annual-statistics --all');

    expect($cache->getAnnualData(2017))->toBeNull();
});
