<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Services\Monitor\Actions\ShuffleValidators;
use Illuminate\Support\Collection;

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
                $difference      = $forgingInfo['currentForger'];
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

    // obsolete
    private static function getActiveValidators(Collection $validators): array
    {
        return $validators->toBase()
            ->map(fn ($validator) => $validator->public_key)
            ->toArray();
    }

    // obsolete
    private static function shuffleValidators(array $validators, int $height): array
    {
        return ShuffleValidators::execute($validators, $height);
    }

    private static function orderValidators(
        array $activeValidators,
        int $currentForger,
        int $validatorCount,
    ): array {
        $validatorsOrdered = [];
        for ($i = $currentForger; $i < $validatorCount + $currentForger; $i++) {
            $validatorsOrdered[] = $activeValidators[$i % $validatorCount];
        }

        return $validatorsOrdered;
    }
}
