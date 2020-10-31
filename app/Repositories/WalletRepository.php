<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\WalletRepository as Contract;
use App\Models\Wallet;

final class WalletRepository implements Contract
{
    public function findByAddress(string $address): Wallet
    {
        return Wallet::where('address', $address)->firstOrFail();
    }

    public function findByPublicKey(string $publicKey): Wallet
    {
        return Wallet::where('public_key', $publicKey)->firstOrFail();
    }

    public function findByUsername(string $username): Wallet
    {
        return Wallet::where('attributes->delegate->username', $username)->firstOrFail();
    }
}
