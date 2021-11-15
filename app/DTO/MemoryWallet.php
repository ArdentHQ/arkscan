<?php

declare(strict_types=1);

namespace App\DTO;

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

    public function publicKey(): ?string
    {
        return $this->publicKey;
    }

    public function username(): ?string
    {
        return (new WalletCache())->getUsernameByAddress($this->address);
    }

    public function isDelegate(): bool
    {
        return ! is_null($this->username());
    }
}
