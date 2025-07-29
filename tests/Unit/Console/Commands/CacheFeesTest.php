<?php

declare(strict_types=1);

use App\Console\Commands\CacheFees;
use App\Models\Transaction;
use App\Services\Cache\FeeCache;
use App\Services\Timestamp;
use Carbon\Carbon;

it('should execute the command', function () {
    Carbon::setTestNow('2021-01-01 00:00:00');

    Transaction::factory(10)->create([
        'gas_price' => 1,
        'timestamp' => Timestamp::now()->subDays(365)->unix(),
    ])->sortByDesc('timestamp');

    Transaction::factory(10)->create([
        'gas_price' => 2,
        'timestamp' => Timestamp::now()->endOfDay()->unix(),
    ])->sortByDesc('timestamp');

    (new CacheFees())->handle($cache = new FeeCache());

    foreach (['day', 'week', 'month', 'quarter', 'year'] as $period) {
        expect($cache->getHistorical($period))->toBeArray();
    }
});
