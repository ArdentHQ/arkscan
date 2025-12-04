<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;
use App\Services\Cache\RequestScopedCache;
use Illuminate\Support\Facades\DB;

final class ValidatorTracker
{
    public static function executeWithCache(array $validators, int $startHeight, ?Block $lastBlock = null): array
    {
        $cacheKey = 'validator_tracker:'.md5(implode(',', $validators).':'.$startHeight.':'.($lastBlock?->number->__toString() ?? '0'));

        return RequestScopedCache::remember($cacheKey, function () use ($validators, $startHeight, $lastBlock) {
            return static::execute($validators, $startHeight, $lastBlock);
        });
    }

    /**
     * @param string[] $validators
     * @param int $startHeight
     * @return array
     */
    public static function execute(array $validators, int $startHeight, ?Block $lastBlock = null): array
    {
        if ($lastBlock === null) {
            throw new \InvalidArgumentException('Last block must be provided.');
        }

        $height = $lastBlock->number->toNumber();

        // Act
        $forgingInfo = ForgingInfoCalculator::calculate($startHeight, $height);

        // Map Next Forgers...
        $forgingIndex = 2; // We start at 2 to skip 0 which results in 0 as time and 1 which would be the next forger.

        // Note: static order will be found by shifting the index based on the forging data from above
        $validatorCount = Network::validatorCount();

        $slotOffset = static::slotOffset($startHeight, $validators);

        return collect($validators)
            ->map(function ($address, $index) use (&$forgingIndex, $forgingInfo, $validatorCount, $slotOffset) {
                return static::determineSlot($address, $index, $forgingIndex, $forgingInfo, $validatorCount, $slotOffset);
            })
            ->toArray();
    }

    private static function determineSlot($address, $index, &$forgingIndex, $forgingInfo, $validatorCount, $slotOffset): array
    {
        // Determine forging order based on the original offset
        $difference       = $forgingInfo['currentForger'] + $slotOffset;
        $normalizedOrder  = $difference >= 0 ? $difference : $validatorCount + $difference;
        $secondsUntilSlot = Network::blockTime() * 1000;

        if ($index === $normalizedOrder) {
            return [
                'address' => $address,
                'status'  => 'next',
                'time'    => $secondsUntilSlot,
                'order'   => $index,
            ];
        }

        if ($index > $normalizedOrder) {
            $nextTime = ($forgingIndex * $secondsUntilSlot);

            $forgingIndex++;

            return [
                'address' => $address,
                'status'  => 'pending',
                'time'    => $nextTime,
                'order'   => $index,
            ];
        }

        return [
            'address' => $address,
            'status'  => 'done',
            'time'    => 0,
            'order'   => $index,
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
    private static function slotOffset(int $roundHeight, array $validators): int
    {
        $roundValidators = DB::connection('explorer')
            ->table('blocks')
            ->select('proposer')
            ->where('number', '>=', $roundHeight)
            ->orderBy('number', 'asc')
            ->get();

        $lastForgerAddress = $roundValidators->last()->proposer;

        $roundBlockCount = $roundValidators->reduce(function ($carry, $item) {
            $count = 1;
            if ($carry->has($item->proposer)) {
                $count = $carry[$item->proposer] + 1;
            }

            $carry->put($item->proposer, $count);

            return $carry;
        }, collect());

        $offset = 0;
        foreach ($validators as $address) {
            $hadBlock = false;
            if ($roundBlockCount->has($address)) {
                $count = $roundBlockCount->get($address) - 1;
                if ($count <= 0) {
                    $roundBlockCount = $roundBlockCount->except($address);
                }

                $hadBlock = true;
            }

            if ($address === $lastForgerAddress) {
                break;
            }

            if ($hadBlock) {
                continue;
            }

            $offset++;
        }

        if ($roundBlockCount->count() > 0) {
            $offset = Network::validatorCount() - $roundBlockCount->sum() + 1;
        }

        return $offset + 1;
    }
}
