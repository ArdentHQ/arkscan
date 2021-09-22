<?php

declare(strict_types=1);

namespace App\Facades;

use App\Contracts\WalletRepository;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;

/**
 * @method static Builder allWithUsername()
 * @method static Builder allWithVote()
 * @method static Builder allWithPublicKey()
 * @method static Builder allWithMultiSignature()
 * @method static Wallet findByAddress(string $address)
 * @method static Wallet findByPublicKey(string $publicKey)
 * @method static Collection findByPublicKeys(array $publicKey)
 * @method static Wallet findByUsername(string $username)
 * @method static Wallet findByIdentifier(string $identifier)
 */
final class Wallets extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return WalletRepository::class;
    }
}
