<?php

declare(strict_types=1);

use App\Console\Commands\CacheDelegateUsernames;
use App\Jobs\CacheUsername;
use App\Models\Wallet;
use Illuminate\Support\Facades\Queue;
use function Tests\configureExplorerDatabase;

it('should execute the command', function () {
    Queue::fake();

    configureExplorerDatabase();

    Wallet::factory(10)->create();

    (new CacheDelegateUsernames())->handle();

    Queue::assertPushed(CacheUsername::class, 10);
});
