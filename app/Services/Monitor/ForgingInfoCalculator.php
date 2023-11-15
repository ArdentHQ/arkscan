<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;

final class ForgingInfoCalculator
{
    public static function calculate(array $delegates, int $roundHeight, int $currentHeight): array
    {
        $slotInfo = (new Slots())->getSlotInfo(null, $currentHeight);

        [$currentForger, $nextForger] = static::findIndex($roundHeight, $currentHeight);

        return [
            'currentForger'  => $currentForger,
            'nextForger'     => $nextForger,
            'blockTimestamp' => $slotInfo['startTime'],
            'canForge'       => $slotInfo['forgingStatus'],
        ];
    }

    private static function findIndex(int $roundHeight, int $currentHeight): array
    {
        $lastSpanSlotNumber = 0;
        $activeDelegates    = Network::delegateCount();

        $currentForger = ($currentHeight - $roundHeight) % $activeDelegates;
        $nextForger    = ($currentForger + 1) % $activeDelegates;

        return [$currentForger, $nextForger];
    }
}
