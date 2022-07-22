<?php

declare(strict_types=1);

namespace App\Exceptions;

use Facade\Ignition\Exceptions\ViewException;
use Illuminate\Support\Str;
use Illuminate\View\ViewException as LaravelViewException;
use Sentry\Event;
use Sentry\EventHint;
use Throwable;

final class Sentry
{
    public static function before(Event $event, ?EventHint $hint) : ?Event
    {
        if ($hint === null || $hint->exception === null) {
            return $event;
        }

        return static::shouldBeIgnored($hint->exception)
                ? null
                : $event;
    }

    private static function shouldBeIgnored(Throwable $exception) : bool
    {
        $ignorables = [
            'filemtime(): stat failed for',
            'Unclosed',
            'Failed to open stream: No such file or directory',
            'File does not exist at path'
        ];

        return ($exception instanceof ViewException || $exception instanceof LaravelViewException)
            && Str::contains($exception->getMessage(), $ignorables);
    }
}
