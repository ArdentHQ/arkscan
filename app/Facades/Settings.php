<?php

declare(strict_types=1);

namespace App\Facades;

use App\Services\Settings as Accessor;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array all()
 * @method static string currency()
 * @method static string locale()
 * @method static bool priceChart()
 * @method static bool feeChart()
 * @method static bool darkTheme()
 * @method static string theme()
 * @method static bool compactTables()
 * @method static bool usesCharts()
 * @method static bool usesPriceChart()
 * @method static bool usesFeeChart()
 * @method static bool usesDarkTheme()
 * @method static bool usesCompactTables()
 */
final class Settings extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Accessor::class;
    }
}
