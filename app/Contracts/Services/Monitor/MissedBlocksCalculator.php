<?php

declare(strict_types=1);

namespace App\Contracts\Services\Monitor;

interface MissedBlocksCalculator
{
    public static function calculateFromHeightGoingBack(int $heightFrom, int $heightTo): array;

    public static function calculateForRound(int $height): array;
}
