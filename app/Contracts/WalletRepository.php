<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Wallet;

interface WalletRepository
{
    public function findByAddress(string $address): Wallet;

    public function findByPublicKey(string $publicKey): Wallet;

    public function findByUsername(string $username): Wallet;
}
