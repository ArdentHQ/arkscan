<?php

declare(strict_types=1);

use App\Console\Commands\CacheDelegatesWithVoters;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Illuminate\Support\Facades\Queue;
use function Tests\configureExplorerDatabase;

it('should execute the command', function () {
    Queue::fake();

    configureExplorerDatabase();

    $delegate = Wallet::factory()->create([
        'attributes' => ['delegate' => ['voteBalance' => 100]],
    ]);

    Wallet::factory(10)->create([
        'attributes' => ['vote' => $delegate->public_key],
    ]);

    (new CacheDelegatesWithVoters())->handle($cache = new WalletCache());

    expect($cache->getVote($delegate->public_key)->is($delegate))->toBeTrue();
});
