<?php

declare(strict_types=1);

namespace App\Facades;

use App\Contracts\TransactionRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Collection allByWallet(string $address, string $publicKey)
 * @method static Collection allBySender(string $publicKey)
 * @method static Collection allByRecipient(string $address)
 */
final class Transactions extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return TransactionRepository::class;
    }
}
