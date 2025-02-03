<?php

declare(strict_types=1);

namespace App\DTO;

use App\Facades\Network;
use App\Services\Cache\ContractCache;
use App\Services\Cache\WalletCache;
use App\Services\Identity;

final class MemoryWallet
{
    private function __construct(public string $address, public ?string $publicKey)
    {
    }

    public static function fromAddress(string $address): self
    {
        return new static($address, null);
    }

    public static function fromPublicKey(string $publicKey): self
    {
        return new static(Identity::address($publicKey), $publicKey);
    }

    public function address(): ?string
    {
        return $this->address;
    }

    public function isContract(): bool
    {
        if (in_array($this->address(), Network::knownContracts(), true)) {
            return true;
        }

        return in_array($this->address(), (new ContractCache())->getContractAddresses(), true);
    }

    public function publicKey(): ?string
    {
        return $this->publicKey;
    }

    public function hasUsername(): bool
    {
        return $this->username() !== null;
    }

    public function username(): ?string
    {
        return (new WalletCache())->getWalletNameByAddress($this->address);
    }

    public function isValidator(): bool
    {
        return (new WalletCache())->getValidator($this->address) !== null;
    }
}
