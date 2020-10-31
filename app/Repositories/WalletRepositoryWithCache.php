<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\WalletRepository;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class WalletRepositoryWithCache implements WalletRepository
{
    use Concerns\ManagesCache;

    private WalletRepository $wallets;

    public function __construct(WalletRepository $wallets)
    {
        $this->wallets = $wallets;
    }

    public function allWithUsername(): Builder
    {
        return $this->remember(fn () => $this->wallets->allWithUsername());
    }

    public function allWithVote(): Builder
    {
        return $this->remember(fn () => $this->wallets->allWithVote());
    }

    public function allWithPublicKey(): Builder
    {
        return $this->remember(fn () => $this->wallets->allWithPublicKey());
    }

    public function findByAddress(string $address): Wallet
    {
        return $this->remember(fn () => $this->wallets->findByAddress($address));
    }

    public function findByPublicKey(string $publicKey): Wallet
    {
        return $this->remember(fn () => $this->wallets->findByPublicKey($publicKey));
    }

    public function findByPublicKeys(array $publicKeys): Collection
    {
        return $this->remember(fn () => $this->wallets->findByPublicKeys($publicKeys));
    }

    public function findByUsername(string $username): Wallet
    {
        return $this->remember(fn () => $this->wallets->findByUsername($username));
    }
}
