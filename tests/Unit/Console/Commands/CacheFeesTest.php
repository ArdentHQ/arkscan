<?php

declare(strict_types=1);

use App\Console\Commands\CacheFees;
use App\Models\Transaction;
use App\Services\Cache\FeeCache;
use App\Services\Timestamp;
use Carbon\Carbon;

it('should execute the command', function () {
    Carbon::setTestNow('2021-01-01 00:00:00');

    $start = Transaction::factory(10)->create([
        'fee'       => '100000000',
        'timestamp' => Timestamp::now()->subDays(365)->unix(),
    ])->sortByDesc('timestamp');

    $end = Transaction::factory(10)->create([
        'fee'       => '200000000',
        'timestamp' => Timestamp::now()->endOfDay()->unix(),
    ])->sortByDesc('timestamp');

    (new CacheFees())->handle($cache = new FeeCache());

    foreach (['day', 'week', 'month', 'quarter', 'year'] as $period) {
        expect($cache->getHistorical($period))->toBeArray();
        expect($cache->getMinimum($period))->toBeFloat();
        expect($cache->getAverage($period))->toBeFloat();
        expect($cache->getMaximum($period))->toBeFloat();
    }

    expect($cache->all('day'))->toHaveKeys(['historical', 'min', 'avg', 'max']);
});
