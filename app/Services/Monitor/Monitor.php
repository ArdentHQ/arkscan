<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Models\Round;
use Illuminate\Support\Collection;

final class Monitor
{
    public static function activeDelegates(int $round): Collection
    {
        return Round::query()
           ->where('round', $round)
           ->orderBy('balance', 'desc')
           ->orderBy('public_key', 'asc')
           ->get();
    }

    public static function roundNumber(): int
    {
        return Round::orderBy('round', 'desc')->firstOrFail()->round;
    }
}
