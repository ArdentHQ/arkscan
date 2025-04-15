<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Round;
use Illuminate\Support\Collection;

/* @phpstan-ignore-next-line */
class MissedBlocksCalculator implements \App\Contracts\Services\Monitor\MissedBlocksCalculator
{
    public static function calculateFromHeightGoingBack(int $heightFrom, int $heightTo): array
    {
        $roundHeightFrom = (int) floor(($heightFrom - Network::validatorCount()) / Network::validatorCount()) * Network::validatorCount() + 1;
        $roundHeightTo   = (int) floor(($heightTo - Network::validatorCount()) / Network::validatorCount()) * Network::validatorCount() + 1;

        $forgingStats = [];
        $rounds       = Round::whereBetween('round_height', [$roundHeightFrom, $roundHeightTo])->orderBy('round', 'asc')->get();
        $rounds->each(function ($round) use (&$forgingStats, $heightTo) {
            $forgingStats = $forgingStats + self::calculateForRound($round, $heightTo);
        });

        return $forgingStats;
    }

    public static function calculateForRound(Round $round, int $heightTo): array
    {
        $roundValidators  = $round->validators;
        $activeValidators = count($roundValidators);

        $producedBlocks = Block::select(['generator_address', 'number', 'timestamp'])
            ->whereBetween('number', [$round->round_height, $round->round_height + $activeValidators - 1])
            ->orderBy('number', 'asc')
            ->get();

        // Good Scenario (no missed block):
        // Round order       [V1,V2,V3,V4,V5]
        // Produced blocks: [V1,V2,V3,V4,V5]

        // Bad Scenario (missed block):
        // Round order       [V1,V2,V3,V4,V5]
        // Produced blocks: [V1,V2,V3,V5,V1] <- V4 missed
        return self::calculateForgingInfo($roundValidators, $producedBlocks);
    }

    private static function calculateForgingInfo(array $roundValidators, Collection $producedBlocks): array
    {
        $forgeInfoByTimestamp = [];
        $misses               = 0;
        $validatorCount       = count($roundValidators);

        $producedBlocks->each(function ($block, $index) use (&$forgeInfoByTimestamp, &$misses, $validatorCount, $roundValidators) {
            $expectedValidator = $roundValidators[($index + $misses) % $validatorCount];
            $actualValidator   = $block['generator_address'];

            $isForger = $actualValidator === $expectedValidator;
            if (! $isForger) {
                $misses += 1;

                // TODO: update stats for actual forger, however this currently gets overridden below since it shares the same timestamp.
                $forgeInfoByTimestamp[strval($block->timestamp)] = [
                    'address' => $actualValidator,
                    'forged'  => true,
                ];
            }

            $forgeInfoByTimestamp[strval($block->timestamp)] = [
                'address' => $expectedValidator,
                'forged'  => $isForger,
            ];
        });

        return $forgeInfoByTimestamp;
    }
}
