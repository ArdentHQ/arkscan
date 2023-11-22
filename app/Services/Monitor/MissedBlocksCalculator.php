<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Block;
use App\Models\Round;
use App\Services\Monitor\Actions\ShuffleDelegates;
use Illuminate\Support\Collection;

/* @phpstan-ignore-next-line */
class MissedBlocksCalculator implements \App\Contracts\Services\Monitor\MissedBlocksCalculator
{
    public static function calculateFromHeightGoingBack(int $heightFrom, int $heightTo): array
    {
        $roundHeightFrom = (int)floor(($heightFrom - Network::delegateCount()) / Network::delegateCount()) * Network::delegateCount() + 1;
        $roundHeightTo = (int)floor(($heightTo - Network::delegateCount()) / Network::delegateCount()) * Network::delegateCount() + 1;

        $forgingStats = [];
        $rounds = Round::whereBetween('round_height', [$roundHeightFrom, $roundHeightTo])->orderBy('round', 'asc')->get();
        $rounds->each(function ($round) use (&$forgingStats, $heightTo) {
            $forgingStats = $forgingStats + self::calculateForRound($round, $heightTo);
        });

        return $forgingStats;
    }

    public static function calculateForRound(Round $round, int $heightTo): array
    {
        $roundValidators = $round->validators;
        $activeDelegates = count($roundValidators);

        $producedBlocks = Block::select(['generator_public_key', 'height', 'timestamp'])
            ->whereBetween('height', [$round->round_height, $round->round_height + $activeDelegates - 1])
            ->orderBy('height', 'asc')
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
        $producedBlocks->each(function ($block, $index) use (&$forgeInfoByTimestamp, $roundValidators) {
            $expectedValidator = $roundValidators[$index];
            $actualValidator = $block['generator_public_key'];

            $forgeInfoByTimestamp[strval($block->timestamp)] = [
                'publicKey' => $actualValidator,
                'forged'    => $actualValidator == $expectedValidator,
            ];
        });

        return $forgeInfoByTimestamp;
    }
}
