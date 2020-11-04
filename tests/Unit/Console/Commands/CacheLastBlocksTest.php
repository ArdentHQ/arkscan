<?php

declare(strict_types=1);

use App\Console\Commands\CacheLastBlocks;
use App\Jobs\CacheLastBlockByPublicKey;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use Illuminate\Support\Facades\Queue;
use function Tests\configureExplorerDatabase;

it('should execute the command', function () {
    Queue::fake();

    configureExplorerDatabase();

    Block::factory()->create();

    Wallet::factory(51)->create()->each(function () {
        Round::factory()->create(['round' => '112168']);
    });

    (new CacheLastBlocks())->handle();

    Queue::assertPushed(CacheLastBlockByPublicKey::class, 51);
});
