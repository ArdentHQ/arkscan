<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

it('forgets the flag for pausing indexing', function ($model) {
    Cache::shouldReceive('forget')->once()->with('scout_indexing_paused_'.$model);

    Artisan::call('scout:resume-indexing', [
        'model' => $model,
    ]);
})->with([
    Transaction::class,
    Wallet::class,
    Block::class,
]);
