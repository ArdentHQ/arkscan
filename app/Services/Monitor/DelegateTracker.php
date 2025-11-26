<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Services\Monitor\Actions\ShuffleDelegates;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class DelegateTracker
{
    public static function execute(Collection $delegates, int $startHeight): array
    {
        // Arrange Block
        $lastBlock = Block::withScope(OrderByHeightScope::class)->firstOrFail();
        $height    = $lastBlock->height->toNumber();

        // Act
        $forgingInfo = ForgingInfoCalculator::calculateCurrentOrder($startHeight, $height);

        // Map Next Forgers...
        $forgingIndex = 2; // We start at 2 to skip 0 which results in 0 as time and 1 which would be the next forger.

        // Note: static order will be found by shifting the index based on the forging data from above
        $delegateCount = Network::delegateCount();

        // Arrange Delegates
        $activeDelegates = self::getActiveDelegates($delegates);

        // TODO: calculate this once for a given round, then cache it as it won't change until next round
        $activeDelegates = self::shuffleDelegates($activeDelegates, $startHeight);

        // Get the original forging info to determine the actual first
        $originalOrder = ForgingInfoCalculator::calculateOriginalOrder(
            Block::where('height', $startHeight)->first()?->timestamp,
            $startHeight
        );

        // // Note: static order will be found by shifting the index based on the forging data from above
        // $delegateCount    = Network::delegateCount();
        $delegatesOrdered = self::orderDelegates(
            $activeDelegates,
            $originalOrder['currentForger'],
            $delegateCount
        );

        $slotOffset = static::slotOffset($startHeight, $delegatesOrdered);

        return collect($delegatesOrdered)
            ->map(function ($publicKey, $index) use (&$forgingIndex, $forgingInfo, $delegateCount, $slotOffset) {
                return static::determineSlot($publicKey, $index, $forgingIndex, $forgingInfo, $delegateCount, $slotOffset);
            })
            ->toArray();
    }

    private static function determineSlot($publicKey, $index, &$forgingIndex, $forgingInfo, $delegateCount, $slotOffset): array
    {
        // Determine forging order based on the original offset
        $difference       = $forgingInfo['currentForger'] + $slotOffset;
        $normalizedOrder  = $difference >= 0 ? $difference : $delegateCount + $difference;
        $secondsUntilSlot = Network::blockTime() * 1000;

        if ($index === $normalizedOrder) {
            return [
                'publicKey' => $publicKey,
                'status'    => 'next',
                'time'      => $secondsUntilSlot,
                'order'     => $index,
            ];
        }

        if ($index > $normalizedOrder) {
            $nextTime = ($forgingIndex - 1) * $secondsUntilSlot;

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
    }

    /**
     * Calculate the slot offset based on missed blocks.
     *
     * @param int $roundHeight
     * @param array $validators
     *
     * @return int
     */
    private static function slotOffset(int $roundHeight, array $delegates): int
    {
        $lastForger = DB::connection('explorer')
            ->table('blocks')
            ->select('generator_public_key', 'timestamp')
            ->where('height', '>=', $roundHeight)
            ->orderBy('height', 'desc')
            ->limit(1)
            ->first();

        if ($lastForger === null) {
            return 0;
        }

        $roundBlockCount = DB::connection('explorer')
            ->table('blocks')
            ->select([
                DB::raw('COUNT(*) as count'),
                'generator_public_key',
            ])
            ->where('height', '>=', $roundHeight)
            ->groupBy('generator_public_key')
            ->get()
            ->pluck('count', 'generator_public_key');

        $forgedCount = $roundBlockCount->sum();

        $counter = 0;
        $offset  = 0;
        foreach ($delegates as $publicKey) {
            // $publicKey = $delegate['public_key'];
            $hadBlock = false;
            if ($roundBlockCount->has($publicKey)) {
                $count = $roundBlockCount->get($publicKey) - 1;
                if ($count <= 0) {
                    $roundBlockCount->forget($publicKey);
                } else {
                    $roundBlockCount->put($publicKey, $count);
                }

                $hadBlock = true;
            }

            $counter++;

            if ($hadBlock) {
                continue;
            }

            if ($counter > $forgedCount) {
                return $offset;
            }

            $offset++;
        }

        if ($roundBlockCount->count() > 0) {
            $offset = Network::delegateCount() - $roundBlockCount->sum() + 1;
        }

        return $offset + 1;
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
