<?php

declare(strict_types=1);

namespace App\Facades\Services;

use Illuminate\Support\Facades\Facade;

/**
 * @method static BigNumber low()
 * @method static BigNumber average()
 * @method static BigNumber high()
 */
final class GasTracker extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \App\Contracts\Services\GasTracker::class;
    }
}
