<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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

        $slotOffset = static::slotOffset($startHeight, $validators);

        return collect($validators)
            ->map(function ($publicKey, $index) use (&$forgingIndex, $forgingInfo, $validatorCount, $slotOffset) {
                return static::determineSlot($publicKey, $index, $forgingIndex, $forgingInfo, $validatorCount, $slotOffset);
            })
            ->toArray();
    }

    private static function determineSlot($publicKey, $index, &$forgingIndex, $forgingInfo, $validatorCount, $slotOffset): array
    {
        // Determine forging order based on the original offset
        $difference       = $forgingInfo['currentForger'] + $slotOffset;
        $normalizedOrder  = $difference >= 0 ? $difference : $validatorCount + $difference;
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
            $nextTime = (($forgingIndex - 1) * $secondsUntilSlot);

            $forgingIndex++;

            return [
                'publicKey' => $publicKey,
                'status'    => 'pending',
                'time'      => $nextTime,
                'order'     => $index,
            ];
        }

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
    private static function slotOffset(int $roundHeight, array $validators): int
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

        $offset = 0;
        foreach ($validators as $publicKey) {
            $hadBlock = false;
            if ($roundBlockCount->has($publicKey)) {
                $count = $roundBlockCount->get($publicKey) - 1;
                if ($count <= 0) {
                    $roundBlockCount = $roundBlockCount->except($publicKey);
                }

                $hadBlock = true;
            }

            if ($publicKey === $lastForger->generator_public_key) {
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

        $lastForgedTimestamp   = $lastForger->timestamp / 1000;
        $secondsSinceLastBlock = (Carbon::now()->unix() - $lastForgedTimestamp);

        if ($secondsSinceLastBlock >= Network::blockTime()) {
            $secondsSinceLastBlock = floor($secondsSinceLastBlock / Network::blockTime()) * Network::blockTime();
            if ($secondsSinceLastBlock >= Network::blockTime()) {
                $missedCount = 0;
                $slotsMissed = 0;

                for ($increment = 0; $increment < $secondsSinceLastBlock; $increment += Network::blockTime()) {
                    $missedCount++;
                    $slotsMissed++;
                    $increment += $missedCount * 2;
                }

                $offset += $slotsMissed;
            }
        }

        return $offset + 1;
    }
}
