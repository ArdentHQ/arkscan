<?php

declare(strict_types=1);

use App\DTO\Inertia\Block as BlockDTO;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\NetworkStatusBlockCache;
use App\Services\ExchangeRate;
use Carbon\Carbon;

it('should make an instance', function () {
    $this->travelTo(Carbon::parse('2023-07-05'));

    $validator = Wallet::factory()->create([
        'attributes' => ['username' => 'test-validator'],
    ]);

    $block = Block::factory()->create([
        'number'             => 12345,
        'timestamp'          => Carbon::parse('2023-07-05')->getTimestampMs(),
        'transactions_count' => 5,
        'reward'             => 2 * 1e18,
        'fee'                => 0.5 * 1e18,
        'proposer'           => $validator->address,
    ]);

    (new NetworkCache())->setHeight(fn (): int => 12400);

    $date = Carbon::parse('2023-07-05')->format('Y-m-d');

    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 3.0);
    (new CryptoDataCache())->setPrices('USD.week', collect([$date => 3.0]));
    (new CryptoDataCache())->setPrices('EUR.week', collect([$date => 2.6]));

    $subject = BlockDTO::fromModel($block);

    expect($subject->hash)->toBe($block->hash);
    expect($subject->number)->toBe(12345);
    expect($subject->transactionCount)->toBe(5);
    expect($subject->reward)->toBe(2.0);
    expect($subject->fee)->toBe(0.5);
    expect($subject->confirmations)->toBe(55);
    expect($subject->exchangeRates)->toBeArray();
    expect($subject->exchangeRates)->toEqual(ExchangeRate::allCurrencyRates($block->timestamp));
    expect($subject->exchangeRates['USD'])->toBe(3.0);
    expect($subject->exchangeRates['EUR'])->toBe(2.6);
});

it('should include exchange rates for all configured currencies', function () {
    $validator = Wallet::factory()->create();

    $block = Block::factory()->create([
        'proposer' => $validator->address,
    ]);

    (new NetworkCache())->setHeight(fn (): int => $block->number->toNumber());

    $subject = BlockDTO::fromModel($block);

    $configuredCurrencies = array_map('strtoupper', array_keys(config('currencies.currencies')));

    expect($subject->exchangeRates)->toBeArray();
    expect(array_keys($subject->exchangeRates))->toEqual($configuredCurrencies);
});
