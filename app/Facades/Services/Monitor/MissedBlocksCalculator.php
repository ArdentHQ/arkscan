<?php

declare(strict_types=1);

namespace App\Facades\Services\Monitor;

use App\Contracts\Services\Monitor\MissedBlocksCalculator as Contract;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array calculateFromHeightGoingBack(int $heightFrom, int $heightTo)
 * @method static array calculateForRound(int $height)
 */
final class MissedBlocksCalculator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Contract::class;
    }
}
