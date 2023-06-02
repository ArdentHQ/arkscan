<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

const EVERY_MINUTE = '* * * * *';

it('schedules the `scout:index-models` command correctly', function () {
    $schedule = app()->make(Schedule::class);

    $events = collect($schedule->events())->filter(function (Event $event) {
        return stripos($event->command, 'scout:index-models');
    });

    if ($events->count() === 0) {
        $this->fail('No events found');
    }

    $events->each(function (Event $event) {
        $this->assertEquals(EVERY_MINUTE, $event->expression);
    });
});
