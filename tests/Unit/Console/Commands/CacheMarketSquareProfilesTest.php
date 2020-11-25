<?php

declare(strict_types=1);

use App\Console\Commands\CacheMarketSquareProfiles;
use App\Contracts\Network;
use App\Jobs\CacheMarketSquareProfileByAddress;
use App\Models\Wallet;
use App\Services\Blockchain\Network as Blockchain;
use Illuminate\Support\Facades\Queue;
use function Tests\configureExplorerDatabase;

it('should execute the command', function () {
    Queue::fake();

    configureExplorerDatabase();

    $this->app->singleton(Network::class, fn () => new Blockchain(config('explorer.networks.production')));

    Wallet::factory()->create();

    (new CacheMarketSquareProfiles())->handle();

    Queue::assertPushed(CacheMarketSquareProfileByAddress::class, 1);
});

it('should not execute the command if not using MarketSquare', function () {
    Queue::fake();

    configureExplorerDatabase();

    $this->app->singleton(Network::class, fn () => new Blockchain(config('explorer.networks.development')));

    Wallet::factory()->create();

    (new CacheMarketSquareProfiles())->handle();

    Queue::assertNotPushed(CacheMarketSquareProfileByAddress::class);
});
