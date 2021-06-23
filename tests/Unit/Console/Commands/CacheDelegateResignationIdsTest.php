<?php

declare(strict_types=1);

use App\Console\Commands\CacheDelegateResignationIds;
use App\Jobs\CacheResignationId;
use App\Models\Transaction;
use Illuminate\Support\Facades\Queue;
use function Tests\configureExplorerDatabase;

it('should execute the command', function () {
    Queue::fake();

    configureExplorerDatabase();

    Transaction::factory(10)->delegateResignation()->create();

    (new CacheDelegateResignationIds())->handle();

    Queue::assertPushed(CacheResignationId::class, 10);
});
