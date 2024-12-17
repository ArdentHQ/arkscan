<?php

declare(strict_types=1);

use App\Console\Commands\CacheFees;
use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\Cache\FeeCache;
use App\Services\Timestamp;
use Carbon\Carbon;

it('should execute the command', function () {
    Carbon::setTestNow('2021-01-01 00:00:00');

    $start = Transaction::factory(10)->create([
        'gas_price' => 1,
        'timestamp' => Timestamp::now()->subDays(365)->unix(),
    ])->sortByDesc('timestamp');

    $end = Transaction::factory(10)->create([
        'gas_price' => 2,
        'timestamp' => Timestamp::now()->endOfDay()->unix(),
    ])->sortByDesc('timestamp');

    (new CacheFees())->handle($cache = new FeeCache());

    foreach (['day', 'week', 'month', 'quarter', 'year'] as $period) {
        expect($cache->getHistorical($period))->toBeArray();
        expect($cache->getMinimum($period))->toBeInstanceOf(BigNumber::class);
        expect($cache->getAverage($period))->toBeInstanceOf(BigNumber::class);
        expect($cache->getMaximum($period))->toBeInstanceOf(BigNumber::class);
    }

    expect($cache->all('day'))->toHaveKeys(['historical', 'min', 'avg', 'max']);
});
