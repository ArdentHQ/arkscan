<?php

declare(strict_types=1);

use App\Models\Transaction;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

it('should call the command and pause/resume indexing', function () {
    Cache::shouldReceive('forever')->once()->with('scout_indexing_paused_'.Transaction::class, true);

    Cache::shouldReceive('forget')->once()->with('scout_indexing_paused_'.Transaction::class);

    Artisan::call('scout:import', [
        'model' => Transaction::class,
    ]);
});

it('should not call the command and pause/resume indexing if --no-pause options is passed', function () {
    Cache::shouldReceive('forever')->never();

    Cache::shouldReceive('forget')->never();

    Artisan::call('scout:import', [
        'model'      => Transaction::class,
        '--no-pause' => true,
    ]);
});
