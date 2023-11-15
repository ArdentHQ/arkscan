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
    /**
     * @param string[] $delegates
     * @param int $startHeight
     * @return array
     */
    public static function execute(array $delegates, int $startHeight): array
    {
        // TODO: revisit all of this for mainsail consensus

        // Arrange Block
        $lastBlock = Block::withScope(OrderByHeightScope::class)->firstOrFail();
        $height    = $lastBlock->height->toNumber();

        // Act
        $forgingInfo = ForgingInfoCalculator::calculate($delegates, $startHeight, $height);

        // Map Next Forgers...
        $forgingIndex = 2; // We start at 2 to skip 0 which results in 0 as time and 1 which would be the next forger.

        // Note: static order will be found by shifting the index based on the forging data from above
        $delegateCount    = Network::delegateCount();

        return collect($delegates)
            ->map(function ($publicKey, $index) use (&$forgingIndex, $forgingInfo, $delegateCount) {
                // Determine forging order based on the original offset
                $difference      = $forgingInfo['currentForger'];
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

    // obsolete
    private static function getActiveDelegates(Collection $delegates): array
    {
        return $delegates->toBase()
            ->map(fn ($delegate) => $delegate->public_key)
            ->toArray();
    }

    // obsolete
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
