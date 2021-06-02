<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;

final class RoundCalculator
{
    public static function calculate(int $height): array
    {
        $result = [
            'round'           => 1,
            'roundHeight'     => 1,
            'nextRound'       => 0,
            'nextRoundHeight' => 0,
            'maxDelegates'    => 0,
        ];

        $activeDelegates    = Network::delegateCount();
        $milestoneHeight    = 1;
        $heightFromLastSpan = $height - $milestoneHeight;
        $currentRound       = (int) floor($heightFromLastSpan / $activeDelegates) + 1;
        $nextRound          = $currentRound + 1;

        $result['round']           = $currentRound;
        $result['roundHeight']     = static::getRoundHeight($currentRound, $activeDelegates);
        $result['nextRound']       = $nextRound;
        $result['nextRoundHeight'] = static::getRoundHeight($nextRound, $activeDelegates);
        $result['maxDelegates']    = $activeDelegates;

        return $result;
    }

    private static function getRoundHeight(int $round, int $activeDelegates): int
    {
        return 1 + ($round - 1) * $activeDelegates;
    }
}
