<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Round;
use App\Services\Monitor\Actions\ShuffleDelegates;

final class MissedBlocksCalculator
{
    public static function calculateFromHeightGoingBack(int $height, int $timeRangeInSeconds): array
    {
        $heightTimestamp     = Block::where('height', $height)->firstOrFail()->timestamp;
        $startHeight         = Block::where('timestamp', '>=', $heightTimestamp - $timeRangeInSeconds)
            ->orderBy('height')
            ->firstOrFail()->height->toNumber();

        $forgingStats = [];
        for ($h = $startHeight; $h <= $height; $h += Network::delegateCount()) {
            $forgingStats = $forgingStats + self::calculateForRound($h);
        }

        return $forgingStats;
    }

    public static function calculateForRound(int $height): array
    {
        $activeDelegates                       = Network::delegateCount();
        $lastRoundInfo                         = RoundCalculator::calculate($height - $activeDelegates);
        $round                                 = $lastRoundInfo['nextRound'];
        $lastRoundLastBlockHeight              = $lastRoundInfo['nextRoundHeight'] - 1;
        $lastRoundLastBlockTs                  = Block::where('height', $lastRoundLastBlockHeight)->firstOrFail()->timestamp;
        $firstBlockInRoundTheoreticalTimestamp = $lastRoundLastBlockTs + Network::blockTime();
        $slotNumberForFirstTheoreticalBlock    = (new Slots())->getSlotInfo($firstBlockInRoundTheoreticalTimestamp)['slotNumber'];

        $delegateOrderForRound = self::calculateDelegateOrder($round, $height, $slotNumberForFirstTheoreticalBlock, $activeDelegates);

        $actualBlocksTimestamps = self::getActualBlocksTimestampsForRound($lastRoundLastBlockHeight, $activeDelegates);

        $theoreticalBlocksByTimestamp = self::getTheoreticalTimestampsForRound(
            $actualBlocksTimestamps,
            $firstBlockInRoundTheoreticalTimestamp,
            $delegateOrderForRound,
            $activeDelegates,
        );

        return self::calculateForgingInfo($theoreticalBlocksByTimestamp, $actualBlocksTimestamps);
    }

    private static function calculateForgingInfo(array $theoreticalBlocksByTimestamp, array $actualBlocksTimestamps): array
    {
        $forgeInfoByTimestamp = [];
        foreach ($theoreticalBlocksByTimestamp as $ts => $delegate) {
            $forgeInfoByTimestamp[$ts] = [
                'publicKey' => $delegate,
                'forged'    => in_array($ts, $actualBlocksTimestamps, true),
            ];
        }

        return $forgeInfoByTimestamp;
    }

    private static function calculateDelegateOrder(
        int $round,
        int $height,
        int $slotNumberForFirstTheoreticalBlock,
        int $activeDelegates,
    ): array {
        $tempDelegateOrderForTheRound        = Round::where('round', $round)->orderByRaw('balance DESC, public_key ASC')->pluck('public_key')->toArray();
        $tempDelegateOrderForTheRound        = ShuffleDelegates::execute($tempDelegateOrderForTheRound, $height);
        $finalDelegateOrderForRound          = array_merge(
            array_slice($tempDelegateOrderForTheRound, $slotNumberForFirstTheoreticalBlock % $activeDelegates),
            array_slice($tempDelegateOrderForTheRound, 0, $slotNumberForFirstTheoreticalBlock % $activeDelegates)
        );

        return $finalDelegateOrderForRound;
    }

    private static function getActualBlocksTimestampsForRound(int $lastRoundLastBlockHeight, int $activeDelegates): array
    {
        return Block::where('height', '>', $lastRoundLastBlockHeight)
            ->where('height', '<=', $lastRoundLastBlockHeight + $activeDelegates)
            ->pluck('timestamp')
            ->toArray();
    }

    private static function getTheoreticalTimestampsForRound(
        array $actualBlocksTimestamps,
        int $firstBlockInRoundTheoreticalTimestamp,
        array $delegateOrderForRound,
        int $activeDelegates,
    ): array {
        $theoreticalBlocksByTimestamp = [];
        $lastActualTimestamp          = count($actualBlocksTimestamps) > 0 ? $actualBlocksTimestamps[count($actualBlocksTimestamps) - 1] : 0;
        for (
            $ts = $firstBlockInRoundTheoreticalTimestamp, $i = 0;
            $ts <= $lastActualTimestamp;
            $ts += Network::blockTime(), $i++
        ) {
            $theoreticalBlocksByTimestamp[strval($ts)] = $delegateOrderForRound[$i % $activeDelegates];
        }

        return $theoreticalBlocksByTimestamp;
    }
}
