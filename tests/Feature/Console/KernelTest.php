<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Config;

const EVERY_MINUTE      = '* * * * *';
const EVERY_FIVE_MINUTE = '*/5 * * * *';

it('schedules the `scout:index-models` command correctly if enabled', function () {
    Config::set('arkscan.scout.run_jobs', true);

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

it('does not schedule the `scout:index-models` command if disabled', function () {
    Config::set('arkscan.scout.run_jobs', false);

    $schedule = app()->make(Schedule::class);

    $events = collect($schedule->events())->filter(function (Event $event) {
        return stripos($event->command, 'scout:index-models');
    });

    expect($events->count())->toBe(0);
});

it('schedules the `explorer:cache-blocks` command if using reverb', function () {
    Config::set('broadcasting.default', 'reverb');

    $schedule = app()->make(Schedule::class);

    $events = collect($schedule->events())->filter(function (Event $event) {
        return stripos($event->command, 'explorer:cache-blocks');
    });

    expect($events->count())->toBe(0);
});

it('does not schedule the `explorer:cache-blocks` command if disabled', function () {
    Config::set('broadcasting.default', null);

    $schedule = app()->make(Schedule::class);

    $events = collect($schedule->events())->filter(function (Event $event) {
        return stripos($event->command, 'explorer:cache-blocks');
    });

    if ($events->count() === 0) {
        $this->fail('No events found');
    }

    $events->each(function (Event $event) {
        $this->assertEquals(EVERY_FIVE_MINUTE, $event->expression);
    });
});
