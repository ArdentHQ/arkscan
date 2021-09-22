<?php

declare(strict_types=1);

namespace App\Facades;

use Carbon\Carbon;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string name()
 * @method static string alias()
 * @method static string api()
 * @method static string explorerTitle()
 * @method static string currency()
 * @method static string currencySymbol()
 * @method static int confirmations()
 * @method static array knownWallets()
 * @method static bool canBeExchanged()
 * @method static bool hasTimelock()
 * @method static Carbon epoch()
 * @method static int delegateCount()
 * @method static int blockTime()
 * @method static int blockReward()
 * @method static \BitWasp\Bitcoin\Network\Network config()
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
