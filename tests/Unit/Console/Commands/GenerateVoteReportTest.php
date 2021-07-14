<?php

declare(strict_types=1);

use App\Jobs\GenerateVoteReport;
use Illuminate\Support\Facades\Bus;

it('dispatches a job to the queue', function () {
    Bus::fake();

    $this->artisan('explorer:generate-vote-report')->assertExitCode(0);

    Bus::assertDispatched(GenerateVoteReport::class);
});
