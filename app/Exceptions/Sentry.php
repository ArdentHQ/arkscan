<?php

declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Support\Str;
use Sentry\Event;
use Sentry\EventHint;

final class Sentry
{
    public static function before(Event $event, ?EventHint $hint) : ?Event
    {
        if ($hint !== null && Str::contains($hint->exception?->getMessage() ?? '', ['filemtime(): stat failed for'])) {
            return null;
        }

        return $event;
    }
}
