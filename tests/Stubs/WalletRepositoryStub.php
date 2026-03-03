<?php

declare(strict_types=1);

namespace Tests\Stubs;

use App\Contracts\WalletRepository;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use LogicException;

class WalletRepositoryStub implements WalletRepository
{
    public int $findByAddressCalls = 0;
    public int $findByPublicKeyCalls = 0;
    public int $findByPublicKeysCalls = 0;
    public int $findByUsernameCalls = 0;
    public int $findByIdentifierCalls = 0;

    public function __construct(private Wallet $wallet)
    {
    }

    public function allWithUsername(): Builder
    {
        throw new LogicException('Not implemented for this stub.');
    }

    public function allWithValidatorPublicKey(): Builder
    {
        throw new LogicException('Not implemented for this stub.');
    }

    public function allWithVote(): Builder
    {
        throw new LogicException('Not implemented for this stub.');
    }

    public function allWithPublicKey(): Builder
    {
        throw new LogicException('Not implemented for this stub.');
    }

    public function findByAddress(string $address): Wallet
    {
        $this->findByAddressCalls++;

        return $this->wallet;
    }

    public function findByPublicKey(string $publicKey): Wallet
    {
        $this->findByPublicKeyCalls++;

        return $this->wallet;
    }

    public function findByPublicKeys(array $publicKey): Collection
    {
        $this->findByPublicKeysCalls++;

        return collect([$this->wallet]);
    }

    public function findByUsername(string $address, bool $caseSensitive = true): Wallet
    {
        $this->findByUsernameCalls++;

        return $this->wallet;
    }

    public function findByIdentifier(string $identifier): Wallet
    {
        $this->findByIdentifierCalls++;

        return $this->wallet;
    }
}
