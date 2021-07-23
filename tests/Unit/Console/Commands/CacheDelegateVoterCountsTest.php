<?php

declare(strict_types=1);

use App\Console\Commands\CacheDelegateVoterCounts;
use App\Jobs\CacheVoterCountByPublicKey;
use App\Models\Wallet;
use Illuminate\Support\Facades\Queue;

it('should execute the command', function () {
    Queue::fake();

    Wallet::factory(10)->create();

    (new CacheDelegateVoterCounts())->handle();

    Queue::assertPushed(CacheVoterCountByPublicKey::class, 10);
});
