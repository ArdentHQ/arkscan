<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;

final class RoundCalculator
{
    public static function calculate(int $height): array
    {
        $result = [
            'round'        => 1,
            'roundHeight'  => 1,
            'nextRound'    => 0,
            'maxDelegates' => 0,
        ];

        $activeDelegates = Network::delegateCount();
        $milestoneHeight = 1;

        $heightFromLastSpan = Block::latestByHeight()->firstOrFail()->height - $milestoneHeight;
        $roundIncrease      = floor($heightFromLastSpan / $activeDelegates);
        $nextRoundIncrease  = ($heightFromLastSpan + 1) % $activeDelegates === 0 ? 1 : 0;

        $result['round'] += $roundIncrease;
        $result['roundHeight'] += $roundIncrease * $activeDelegates;
        $result['nextRound']    = $result['round'] + $nextRoundIncrease;
        $result['maxDelegates'] = $activeDelegates;

        return $result;
    }
}
