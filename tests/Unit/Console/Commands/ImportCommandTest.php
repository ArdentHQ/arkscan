<?php

declare(strict_types=1);

use App\Console\Commands\CacheFees;
use App\Console\Commands\ImportCommand;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\FeeCache;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Facades\Cache;

it('should call the command and pause/resume indexing', function () {
    Cache::shouldReceive('forever')->once()->with('scout_indexing_paused_'.Transaction::class, true);

    Cache::shouldReceive('forget')->once()->with('scout_indexing_paused_'.Transaction::class);

    Artisan::call('scout:import', [
        'model' => Transaction::class,
    ]);
});
