<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;

final class ForgingInfoCalculator
{
    public function getBlockTimeLookup(int $height): int
    {
        return Block::where('height', $height)->firstOrFail()->timestamp;
    }

    public function calculateForgingInfo(int $timestamp, int $height): array
    {
        $slotInfo = (new Slots())->getSlotInfo($timestamp, $height);

        [$currentForger, $nextForger] = $this->findIndex($slotInfo['slotNumber']);

        return [
            'currentForger'  => $currentForger,
            'nextForger'     => $nextForger,
            'blockTimestamp' => $slotInfo['startTime'],
            'canForge'       => $slotInfo['forgingStatus'],
        ];
    }

    private function findIndex(int $slotNumber): array
    {
        $lastSpanSlotNumber = 0;
        $activeDelegates    = Network::delegateCount();

        $currentForger = ($slotNumber - $lastSpanSlotNumber) % $activeDelegates;
        $nextForger    = ($currentForger + 1) % $activeDelegates;

        return [$currentForger, $nextForger];
    }
}
