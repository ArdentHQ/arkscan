<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\WalletRepository;
use App\Models\Wallet;

final class WalletRepositoryWithCache implements WalletRepository
{
    use Concerns\ManagesCache;

    private WalletRepository $wallets;

    public function __construct(WalletRepository $wallets)
    {
        $this->wallets = $wallets;
    }

    public function findByAddress(string $address): Wallet
    {
        return $this->remember(fn () => $this->wallets->findByAddress($address));
    }

    public function findByPublicKey(string $publicKey): Wallet
    {
        return $this->remember(fn () => $this->wallets->findByPublicKey($publicKey));
    }

    public function findByUsername(string $username): Wallet
    {
        return $this->remember(fn () => $this->wallets->findByUsername($username));
    }
}
