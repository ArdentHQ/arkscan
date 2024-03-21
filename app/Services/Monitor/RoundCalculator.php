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
            'maxValidators'    => 0,
        ];

        $activeValidators    = Network::validatorCount();
        $milestoneHeight    = 1;
        $heightFromLastSpan = $height - $milestoneHeight;
        $currentRound       = (int) floor($heightFromLastSpan / $activeValidators) + 1;
        $nextRound          = $currentRound + 1;

        $result['round']           = $currentRound;
        $result['roundHeight']     = static::getRoundHeight($currentRound, $activeValidators);
        $result['nextRound']       = $nextRound;
        $result['nextRoundHeight'] = static::getRoundHeight($nextRound, $activeValidators);
        $result['maxValidators']    = $activeValidators;

        return $result;
    }

    private static function getRoundHeight(int $round, int $activeValidators): int
    {
        return 1 + ($round - 1) * $activeValidators;
    }
}
