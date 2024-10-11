<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Round;
use Illuminate\Support\Facades\Cache;

final class Monitor
{
    public const ROUND_FOR_HEIGHT_TTL = 600; // 10 minutes - the round number should never change for a given height

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
        return (int) Cache::remember('round:height:'.$height, self::ROUND_FOR_HEIGHT_TTL, function () use ($height) {
            return Round::where('round_height', '<=', $height)
                ->orderBy('round', 'desc')
                ->firstOrFail()
                ->round;
        });
    }
}
