<?php

declare(strict_types=1);

use App\Console\Commands\CacheValidatorsWithVoters;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Illuminate\Support\Facades\Queue;

it('should execute the command', function () {
    Queue::fake();

    $validator = Wallet::factory()->create([
        'attributes' => ['validatorVoteBalance' => 100],
    ]);

    (new CacheValidatorsWithVoters())->handle($cache = new WalletCache());

    expect($cache->getVote($validator->address)->is($validator))->toBeTrue();
});
