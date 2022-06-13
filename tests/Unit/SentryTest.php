<?php

declare(strict_types=1);

use App\Exceptions\Sentry;
use Facade\Ignition\Exceptions\ViewException as FacadeViewException;
use Illuminate\View\ViewException;
use Sentry\Event;
use Sentry\EventHint;
use Sentry\EventType;

it('ignores if event hint is null', function () {
    $event = Event::createEvent();
    $hint = null;

    $result = Sentry::before($event, $hint);

    expect($result)->toBe($event);
});

it('ignores if exception is not set', function () {
    $event = Event::createEvent();
    $hint = EventHint::fromArray([
        //
    ]);

    $result = Sentry::before($event, $hint);

    expect($result)->toBe($event);
});

it('does not report laravel view exceptions that contain specific messages', function () {
    $event = Event::createEvent();

    $result = Sentry::before($event, EventHint::fromArray([
        'exception' => new ViewException('unclosed'),
    ]));

    expect($result)->toBeNull();

    $result = Sentry::before($event, EventHint::fromArray([
        'exception' => new ViewException('filemtime(): stat failed for'),
    ]));

    expect($result)->toBeNull();

    $result = Sentry::before($event, EventHint::fromArray([
        'exception' => new ViewException('undefined array key'),
    ]));

    expect($result)->toBe($event);
});

it('does not report spatie view exceptions that contain specific messages', function () {
    $event = Event::createEvent();

    $result = Sentry::before($event, EventHint::fromArray([
        'exception' => new FacadeViewException('unclosed'),
    ]));

    expect($result)->toBeNull();

    $result = Sentry::before($event, EventHint::fromArray([
        'exception' => new FacadeViewException('filemtime(): stat failed for'),
    ]));

    expect($result)->toBeNull();

    $result = Sentry::before($event, EventHint::fromArray([
        'exception' => new FacadeViewException('undefined array key'),
    ]));

    expect($result)->toBe($event);
});

it('reports all other exceptions even if they contain specific messages', function () {
    $event = Event::createEvent();

    $result = Sentry::before($event, EventHint::fromArray([
        'exception' => new RuntimeException('unclosed'),
    ]));

    expect($result)->toBe($event);

    $result = Sentry::before($event, EventHint::fromArray([
        'exception' => new RuntimeException('filemtime(): stat failed for'),
    ]));

    expect($result)->toBe($event);

    $result = Sentry::before($event, EventHint::fromArray([
        'exception' => new RuntimeException('undefined array key'),
    ]));

    expect($result)->toBe($event);
});
