<?php

declare(strict_types=1);

use App\Console\Commands\CacheVoterCount;
use App\Jobs\CacheVoterCountByPublicKey;
use App\Models\Wallet;
use Illuminate\Support\Facades\Queue;
use function Tests\configureExplorerDatabase;

it('should execute the command', function () {
    Queue::fake();

    configureExplorerDatabase();

    Wallet::factory(10)->create();

    (new CacheVoterCount())->handle();

    Queue::assertPushed(CacheVoterCountByPublicKey::class, 10);
});
