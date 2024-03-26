<?php

declare(strict_types=1);

use App\Console\Commands\CacheValidatorResignationIds;
use App\Jobs\CacheResignationId;
use App\Models\Transaction;
use Illuminate\Support\Facades\Queue;

it('should execute the command', function () {
    Queue::fake();

    Transaction::factory(10)->validatorResignation()->create();

    (new CacheValidatorResignationIds())->handle();

    Queue::assertPushed(CacheResignationId::class, 10);
});
