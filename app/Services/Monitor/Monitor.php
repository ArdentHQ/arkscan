<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Facades\Rounds;

final class Monitor
{
    public static function roundNumber(): int
    {
        return Rounds::currentRound()->round;
    }

    public static function heightRangeByRound(int $round): array
    {
        $roundStart = (int) ($round - 1) * Network::delegateCount();

        return [$roundStart, $roundStart + 50];
    }
}
