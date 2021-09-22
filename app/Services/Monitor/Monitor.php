<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Facades\Rounds;

final class Monitor
{
    public static function roundNumber(): int
    {
        return Rounds::current();
    }

    public static function heightRangeByRound(int $round): array
    {
        $delegateCount = Network::delegateCount();
        $roundStart    = (int) (($round - 1) * $delegateCount) + 1;

        return [$roundStart, $roundStart + ($delegateCount - 1)];
    }

    public static function roundNumberFromHeight(int $height): int
    {
        $delegateCount = Network::delegateCount();

        return (int) ceil($height / $delegateCount);
    }
}
