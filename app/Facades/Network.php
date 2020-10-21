<?php

declare(strict_types=1);

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string name()
 * @method static string alias()
 * @method static string currency()
 * @method static string currencySymbol()
 * @method static int confirmations()
 * @method static array knownWallets()
 * @method static bool canBeExchanged()
 * @method static string host()
 * @method static bool usesMarketsquare()
 * @method static \Carbon\Carbon epoch()
 */
final class Network extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \App\Contracts\Network::class;
    }
}
