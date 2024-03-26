<?php

declare(strict_types=1);

use App\Console\Commands\CacheValidatorsWithVoters;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Illuminate\Support\Facades\Queue;

it('should execute the command', function () {
    Queue::fake();

    $validator = Wallet::factory()->create([
        'attributes' => ['validator' => ['voteBalance' => 100]],
    ]);

    Wallet::factory(10)->create([
        'attributes' => ['vote' => $validator->public_key],
    ]);

    (new CacheValidatorsWithVoters())->handle($cache = new WalletCache());

    expect($cache->getVote($validator->public_key)->is($validator))->toBeTrue();
});
