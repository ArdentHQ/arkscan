<?php

declare(strict_types=1);

namespace App\Contracts\Services\Monitor;

use App\Models\Round;

interface MissedBlocksCalculator
{
    public static function calculateFromHeightGoingBack(int $heightFrom, int $heightTo): array;

    public static function calculateForRound(Round $round, int $heightTo): array;
}
