<?php

declare(strict_types=1);

use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\Statistics;

it('should cache address holdings', function () {
    $cache = new Statistics();

    Wallet::factory()->create([
        'balance' => 1.1 * 1e8,
    ]);
    Wallet::factory()->count(1)->create([
        'balance' => 1 * 1e8,
    ]);
    Wallet::factory()->count(4)->create([
        'balance' => 0.9 * 1e8,
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getAddressHoldings())->toBe([
        ['grouped' => 0, 'count' => 5],
        ['grouped' => 1, 'count' => 1]
    ]);

    Wallet::factory()->count(5)->create([
        'balance' => 10.1 * 1e8,
    ]);
    Wallet::factory()->count(3)->create([
        'balance' => 1000.1 * 1e8,
    ]);
    Wallet::factory()->count(2)->create([
        'balance' => BigNumber::new(1000000.1 * 1e8),
    ]);

    $this->artisan('explorer:cache-address-statistics');

    expect($cache->getAddressHoldings())->toBe([
        ['grouped' => 0, 'count' => 5],
        ['grouped' => 1, 'count' => 6],
        ['grouped' => 1000, 'count' => 3],
        ['grouped' => 1000000, 'count' => 2],
    ]);
});
