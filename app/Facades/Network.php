<?php

declare(strict_types=1);

namespace App\Facades;

use App\Services\BigNumber;
use Carbon\Carbon;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string coin()
 * @method static string name()
 * @method static string alias()
 * @method static string api()
 * @method static string explorerTitle()
 * @method static string currency()
 * @method static string currencySymbol()
 * @method static string nethash()
 * @method static int confirmations()
 * @method static string knownWalletsUrl()
 * @method static array knownWallets()
 * @method static array knownContracts()
 * @method static ?string knownContract(string $name)
 * @method static ?string contractMethod(string $name, string $default)
 * @method static bool canBeExchanged()
 * @method static Carbon epoch()
 * @method static int validatorCount()
 * @method static int blockTime()
 * @method static int blockReward()
 * @method static int base58Prefix()
 * @method static BigNumber supply()
 * @method static \ArkEcosystem\Crypto\Networks\AbstractNetwork config()
 * @method static array toArray()
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
