<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

it('stores in cache a flag for pausing indexing', function ($model) {
    Cache::shouldReceive('forever')->once()->with('scout_indexing_paused_'.$model, true);

    Artisan::call('scout:pause-indexing', [
        'model' => $model,
    ]);
})->with([
    Transaction::class,
    Wallet::class,
    Block::class,
]);
