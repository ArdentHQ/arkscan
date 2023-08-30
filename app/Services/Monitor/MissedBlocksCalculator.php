<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Round;
use App\Services\Monitor\Actions\ShuffleDelegates;
use Illuminate\Support\Facades\DB;

/* @phpstan-ignore-next-line */
class MissedBlocksCalculator
{
    static ?int $delegateCount = null;

    static ?int $blockTime = null;

    static float $timeStart;

    static function timer(?string $title = null)
    {
        $timeEnd = microtime(true);
        if (isset(static::$timeStart) && $title) {
            echo $title.' - '.number_format($timeEnd - static::$timeStart, 4)."\n";
        }

        static::$timeStart = microtime(true);
    }

    public static function calculateFromHeightGoingBack(int $heightFrom, int $heightTo): array
    {
        $delegateCount = static::delegateCount();
        $forgingStats = [];
        for ($h = $heightFrom; $h <= $heightTo; $h += $delegateCount) {
            $forgingStats = $forgingStats + self::calculateForRound($h);
            static::timer('calculateForgingInfo');
        }

        return $forgingStats;
    }

    public static function calculateForRound(int $height): array
    {
        $activeDelegates                       = static::delegateCount();
        static::timer();
        $lastRoundInfo                         = RoundCalculator::calculate($height - $activeDelegates);
        $round                                 = $lastRoundInfo['nextRound'];
        $lastRoundLastBlockHeight              = $lastRoundInfo['nextRoundHeight'] - 1;
        static::timer('RoundCalculator');
        $lastRoundLastBlockTs                  = Block::where('height', $lastRoundLastBlockHeight)->firstOrFail()->timestamp; ####
        static::timer('Block::where');
        $firstBlockInRoundTheoreticalTimestamp = $lastRoundLastBlockTs + static::blockTime();
        $slotNumberForFirstTheoreticalBlock    = (new Slots())->getSlotInfo($firstBlockInRoundTheoreticalTimestamp)['slotNumber'];
        static::timer('getSlotInfo');

        $delegateOrderForRound = self::calculateDelegateOrder($round, $height, $slotNumberForFirstTheoreticalBlock, $activeDelegates);
        static::timer('calculateDelegateOrder');

        $actualBlocksTimestamps = self::getActualBlocksTimestampsForRound($lastRoundLastBlockHeight, $activeDelegates); ####
        static::timer('getActualBlocksTimestampsForRound');

        $theoreticalBlocksByTimestamp = self::getTheoreticalTimestampsForRound(
            $actualBlocksTimestamps,
            $firstBlockInRoundTheoreticalTimestamp,
            $delegateOrderForRound,
            $activeDelegates,
        );
        static::timer('getTheoreticalTimestampsForRound');

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
        // dump('Round::where');
        $tempDelegateOrderForTheRound        = Round::where('round', $round)->orderByRaw('balance DESC, public_key ASC')->pluck('public_key')->toArray(); ###
        // dump('ShuffleDelegates');
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
            $ts += static::blockTime(), $i++
        ) {
            $theoreticalBlocksByTimestamp[strval($ts)] = $delegateOrderForRound[$i % $activeDelegates];
        }

        return $theoreticalBlocksByTimestamp;
    }

    private static function blockTime(): int
    {
        if (static::$blockTime !== null) {
            return static::$blockTime;
        }

        return static::$blockTime = Network::blockTime();
    }

    private static function delegateCount(): int
    {
        if (static::$delegateCount !== null) {
            return static::$delegateCount;
        }

        return static::$delegateCount = Network::delegateCount();
    }
}
