<?php

declare(strict_types=1);

use App\Console\Commands\CacheValidatorProductivity;
use App\Facades\Network;
use App\Jobs\CacheProductivityByPublicKey;
use App\Models\Wallet;
use Illuminate\Support\Facades\Queue;
use function Tests\createRoundEntry;

it('should execute the command', function () {
    Queue::fake();

    $wallets = Wallet::factory(Network::validatorCount())->create();

    createRoundEntry(112168, 112168 * Network::validatorCount(), $wallets);

    (new CacheValidatorProductivity())->handle();

    Queue::assertPushed(CacheProductivityByPublicKey::class, 53);
});
