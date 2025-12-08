<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;

final class ForgingInfoCalculator
{
    public static function calculateCurrentOrder(int $roundHeight, int $currentHeight): array
    {
        [$currentForger, $nextForger] = static::findCurrentIndex($roundHeight, $currentHeight);

        return [
            'currentForger' => $currentForger,
            'nextForger'    => $nextForger,
        ];
    }

    public static function calculateOriginalOrder(?int $timestamp, int $height): array
    {
        $slotInfo = (new Slots())->getSlotInfo($timestamp, $height);

        [$currentForger, $nextForger] = static::findOriginalIndex($slotInfo['slotNumber']);

        return [
            'currentForger'  => $currentForger,
            'nextForger'     => $nextForger,
            'blockTimestamp' => $slotInfo['startTime'],
            'canForge'       => $slotInfo['forgingStatus'],
        ];
    }

    private static function findCurrentIndex(int $roundHeight, int $currentHeight): array
    {
        $activeDelegates    = Network::delegateCount();

        $currentForger = ($currentHeight - $roundHeight) % $activeDelegates;
        $nextForger    = ($currentForger + 1) % $activeDelegates;

        return [$currentForger, $nextForger];
    }

    private static function findOriginalIndex(int $slotNumber): array
    {
        $lastSpanSlotNumber = 0;
        $activeDelegates    = Network::delegateCount();

        $currentForger = ($slotNumber - $lastSpanSlotNumber) % $activeDelegates;
        $nextForger    = ($currentForger + 1) % $activeDelegates;

        return [$currentForger, $nextForger];
    }
}
