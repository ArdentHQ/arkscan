<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Event;
use Tests\Stubs\StatisticsCommandStub;

it('should not trigger event if not statistics event', function () {
    Event::fake();

    (new StatisticsCommandStub())->noEventTrigger();

    Event::assertNothingDispatched();
});
