<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Round;

final class Monitor
{
    public static function roundNumber(): int
    {
        return Rounds::current()->round;
    }

    public static function heightRangeByRound(Round $round): array
    {
        $validatorCount = Network::validatorCount();

        return [$round->round_height, $round->round_height + ($validatorCount - 1)];
    }

    public static function roundNumberFromHeight(int $height): int
    {
        return Round::where('round_height', '<=', $height)
            ->orderBy('round_height')
            ->firstOrFail()
            ->round;
    }
}
