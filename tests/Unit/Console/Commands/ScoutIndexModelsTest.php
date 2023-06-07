<?php

declare(strict_types=1);

use App\Jobs\IndexBlocks;
use App\Jobs\IndexTransactions;
use App\Jobs\IndexWallets;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Bus::fake([
        IndexTransactions::class,
        IndexWallets::class,
        IndexBlocks::class,
    ]);

    Http::fake([
        '*' => Http::response([
            'hits' => [
                ['timestamp' => 5],
            ],
        ]),
    ]);
});

it('dispatches the jobs by default', function () {
    Artisan::call('scout:index-models');

    Bus::assertDispatched(IndexTransactions::class);
    Bus::assertDispatched(IndexWallets::class);
    Bus::assertDispatched(IndexBlocks::class);
});

it('does not dispatch the jobs while paused', function ($model, $job) {
    Artisan::call('scout:pause-indexing', [
        'model' => $model,
    ]);

    Artisan::call('scout:index-models');

    Bus::assertNotDispatched($job);

    // Resume indexing
    Artisan::call('scout:resume-indexing', [
        'model' => $model,
    ]);

    Artisan::call('scout:index-models');

    Bus::assertDispatched($job);
})->with([
    [Transaction::class, IndexTransactions::class],
    [Wallet::class, IndexWallets::class],
    [Block::class, IndexBlocks::class],
]);
