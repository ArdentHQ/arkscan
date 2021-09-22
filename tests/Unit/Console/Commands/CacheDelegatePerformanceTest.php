<?php

declare(strict_types=1);

use App\Console\Commands\CacheDelegatePerformance;
use App\Jobs\CachePastRoundPerformanceByPublicKey;
use App\Models\Round;
use App\Models\Wallet;
use Illuminate\Support\Facades\Queue;

it('should execute the command', function () {
    Queue::fake();

    Wallet::factory(51)->create()->each(function () {
        Round::factory()->create(['round' => '112168']);
    });

    (new CacheDelegatePerformance())->handle();

    Queue::assertPushed(CachePastRoundPerformanceByPublicKey::class, 51);
});
