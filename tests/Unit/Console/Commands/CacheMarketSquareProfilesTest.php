<?php

declare(strict_types=1);

use App\Console\Commands\CacheMarketSquareProfiles;
use App\Jobs\CacheMarketSquareProfileByAddress;
use App\Models\Wallet;
use Illuminate\Support\Facades\Queue;
use function Tests\configureExplorerDatabase;

it('should execute the command', function () {
    Queue::fake();

    configureExplorerDatabase();

    Wallet::factory()->create();

    (new CacheMarketSquareProfiles())->handle();

    Queue::assertPushed(CacheMarketSquareProfileByAddress::class, 1);
});
