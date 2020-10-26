<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;

final class RoundCalculator
{
    public static function calculate(int $height): array
    {
        $activeDelegates    = Network::delegateCount();
        $milestoneHeight    = 1;
        $heightFromLastSpan = $height - $milestoneHeight;
        $currentRound       = (int) floor($heightFromLastSpan / $activeDelegates);
        $nextRound          = $currentRound + 1;

        return [
            'round'           => $currentRound,
            'roundHeight'     => $currentRound * $activeDelegates,
            'nextRound'       => $nextRound,
            'nextRoundHeight' => $nextRound * $activeDelegates,
            'maxDelegates'    => $activeDelegates,
        ];
    }
}
