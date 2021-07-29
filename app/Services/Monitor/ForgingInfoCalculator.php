<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;

final class ForgingInfoCalculator
{
    public static function calculate(?int $timestamp, int $height): array
    {
        $slotInfo = (new Slots())->getSlotInfo($timestamp, $height);

        [$currentForger, $nextForger] = static::findIndex($slotInfo['slotNumber']);

        return [
            'currentForger'  => $currentForger,
            'nextForger'     => $nextForger,
            'blockTimestamp' => $slotInfo['startTime'],
            'canForge'       => $slotInfo['forgingStatus'],
        ];
    }

    private static function findIndex(int $slotNumber): array
    {
        $lastSpanSlotNumber = 0;
        $activeDelegates    = Network::delegateCount();

        $currentForger = ($slotNumber - $lastSpanSlotNumber) % $activeDelegates;
        $nextForger    = ($currentForger + 1) % $activeDelegates;

        return [$currentForger, $nextForger];
    }
}
