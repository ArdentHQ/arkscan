<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Services\Monitor\Actions\ShuffleDelegates;
use Illuminate\Support\Collection;

final class DelegateTracker
{
    public static function execute(Collection $delegates, int $startHeight): array
    {
        // Arrange Block
        $lastBlock = Block::withScope(OrderByHeightScope::class)->firstOrFail();
        $height    = $lastBlock->height->toNumber();

        // Arrange Delegates
        $activeDelegates = self::getActiveDelegates($delegates);

        // TODO: calculate this once for a given round, then cache it as it won't change until next round
        $activeDelegates = self::shuffleDelegates($activeDelegates, $startHeight);

        // Act
        $forgingInfo = ForgingInfoCalculator::calculate(null, $height);

        // // Determine Next Forgers...
        // $nextForgers = [];
        // for ($i = 0; $i < $maxDelegates; $i++) {
        //     $delegate = $activeDelegates[($forgingInfo['currentForger'] + $i) % $maxDelegates];

        //     if ($delegate) {
        //         $nextForgers[] = $delegate;
        //     }
        // }

        // Map Next Forgers...
        $forgingIndex = 2; // We start at 2 to skip 0 which results in 0 as time and 1 which would be the next forger.

        // Get the original forging info to determine the actual first
        $originalOrder = ForgingInfoCalculator::calculate(
            Block::where('height', $startHeight)->firstOrFail()->timestamp,
            $startHeight
        );

        // Note: static order will be found by shifting the index based on the forging data from above
        $delegateCount    = Network::delegateCount();
        $delegatesOrdered = self::orderDelegates(
            $activeDelegates,
            $originalOrder['currentForger'],
            $delegateCount
        );

        return collect($delegatesOrdered)
            ->map(function ($publicKey, $index) use (&$forgingIndex, $forgingInfo, $originalOrder, $delegateCount) {

                // Determine forging order based on the original offset
                $difference      = $forgingInfo['currentForger'] - $originalOrder['currentForger'];
                $normalizedOrder = $difference >= 0 ? $difference : $delegateCount + $difference;

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

    private static function getActiveDelegates(Collection $delegates): array
    {
        return $delegates->toBase()
            ->map(fn ($delegate) => $delegate->public_key)
            ->toArray();
    }

    private static function shuffleDelegates(array $delegates, int $height): array
    {
        return ShuffleDelegates::execute($delegates, $height);
    }

    private static function orderDelegates(
        array $activeDelegates,
        int $currentForger,
        int $delegateCount,
    ): array {
        $delegatesOrdered = [];
        for ($i = $currentForger; $i < $delegateCount + $currentForger; $i++) {
            $delegatesOrdered[] = $activeDelegates[$i % $delegateCount];
        }

        return $delegatesOrdered;
    }
}
