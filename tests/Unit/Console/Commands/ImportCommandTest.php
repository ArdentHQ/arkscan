<?php

declare(strict_types=1);

use App\Models\Transaction;
use Illuminate\Support\Facades\Artisan;

use function Tests\mockTaggedCache;

it('should call the command and pause/resume indexing', function () {
    mockTaggedCache(withTags: true)->shouldReceive('forever')->once()->with('scout_indexing_paused_'.Transaction::class, true);

    mockTaggedCache(withTags: true)->shouldReceive('forget')->once()->with('scout_indexing_paused_'.Transaction::class);

    Artisan::call('scout:import', [
        'model' => Transaction::class,
    ]);
});
