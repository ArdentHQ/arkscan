<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\WalletRepository;
use App\Models\Wallet;
use App\Repositories\Concerns\ManagesCache;
use Illuminate\Cache\TaggedCache;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class WalletRepositoryWithCache implements WalletRepository
{
    use ManagesCache;

    public function __construct(private WalletRepository $wallets)
    {
    }

    public function allWithUsername(): Builder
    {
        return $this->wallets->allWithUsername();
    }

    public function allWithVote(): Builder
    {
        return $this->wallets->allWithVote();
    }

    public function allWithPublicKey(): Builder
    {
        return $this->wallets->allWithPublicKey();
    }

    public function allWithMultiSignature(): Builder
    {
        return $this->wallets->allWithMultiSignature();
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

    public function findByIdentifier(string $identifier): Wallet
    {
        return $this->remember(fn () => $this->wallets->findByIdentifier($identifier));
    }

    private function getCache(): TaggedCache
    {
        return Cache::tags('wallets');
    }
}
