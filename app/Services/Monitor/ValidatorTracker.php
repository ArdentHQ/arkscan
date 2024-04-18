<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;

final class ValidatorTracker
{
    /**
     * @param string[] $validators
     * @param int $startHeight
     * @return array
     */
    public static function execute(array $validators, int $startHeight): array
    {
        // Arrange Block
        $lastBlock = Block::withScope(OrderByHeightScope::class)->firstOrFail();
        $height    = $lastBlock->height->toNumber();

        // Act
        $forgingInfo = ForgingInfoCalculator::calculate($startHeight, $height);

        // Map Next Forgers...
        $forgingIndex = 2; // We start at 2 to skip 0 which results in 0 as time and 1 which would be the next forger.

        // Note: static order will be found by shifting the index based on the forging data from above
        $validatorCount    = Network::validatorCount();

        return collect($validators)
            ->map(function ($publicKey, $index) use (&$forgingIndex, $forgingInfo, $validatorCount) {
                // Determine forging order based on the original offset
                $difference      = $forgingInfo['currentForger']; // should this be nextForger? The "next" delegate has already forged a block based on the height
                $normalizedOrder = $difference >= 0 ? $difference : $validatorCount + $difference;

                if ($index === $normalizedOrder) {
                    return [
                        'publicKey' => $publicKey,
                        'status'    => 'next',
                        'time'      => Network::blockTime() * 1000,
                        'order'     => $index,
                    ];
                }

                if ($index > $normalizedOrder) {
                    $nextTime = (($forgingIndex) * Network::blockTime() * 1000);

                    $forgingIndex++;

                    return [
                        'publicKey' => $publicKey,
                        'status'    => 'pending',
                        'time'      => $nextTime,
                        'order'     => $index,
                    ];
                }

                // TODO: we need to handle missed blocks by moving "done" states back to pending when needed
                return [
                    'publicKey' => $publicKey,
                    'status'    => 'done',
                    'time'      => 0,
                    'order'     => $index,
                ];
            })
            ->toArray();
    }
}
