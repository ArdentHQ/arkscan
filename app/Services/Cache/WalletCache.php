<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Models\Wallet;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class WalletCache implements Contract
{
    use Concerns\ManagesCache;

    public function getKnown(): array
    {
        return $this->get('known', []);
    }

    public function setKnown(\Closure $callback): array
    {
        return $this->remember('known', now()->addDay(), $callback);
    }

    public function getLastBlock(string $publicKey): array
    {
        return $this->get(sprintf('last_block/%s', $publicKey), []);
    }

    public function setLastBlock(string $publicKey, \Closure $callback): array
    {
        return $this->remember(sprintf('last_block/%s', $publicKey), now()->addMinute(), $callback);
    }

    public function getPerformance(string $publicKey): array
    {
        return $this->get(sprintf('performance/%s', $publicKey), []);
    }

    public function setPerformance(string $publicKey, \Closure $callback): array
    {
        return $this->remember(sprintf('performance/%s', $publicKey), now()->addHour(), $callback);
    }

    public function getProductivity(string $publicKey): float
    {
        return (float) $this->get(sprintf('productivity/%s', $publicKey), 0);
    }

    public function setProductivity(string $publicKey, \Closure $callback): float
    {
        return (float) $this->remember(sprintf('productivity/%s', $publicKey), now()->addMinute(), $callback);
    }

    public function getResignationId(string $address): ?string
    {
        return $this->get(sprintf('resignation_id/%s', $address));
    }

    public function setResignationId(string $address, \Closure $callback): string
    {
        return $this->remember(sprintf('resignation_id/%s', $address), now()->addMinute(), $callback);
    }

    public function getVote(string $publicKey): ?Wallet
    {
        return $this->get(sprintf('vote/%s', $publicKey));
    }

    public function setVote(string $publicKey, \Closure $callback): ?Wallet
    {
        return $this->remember(sprintf('vote/%s', $publicKey), now()->addMinute(), $callback);
    }

    public function getMultiSignatureAddress(int $min, array $publicKeys): ?string
    {
        return $this->get(sprintf('multi_signature/%s/%s', $min, serialize($publicKeys)));
    }

    public function setMultiSignatureAddress(int $min, array $publicKeys, \Closure $callback): string
    {
        return $this->remember(sprintf('multi_signature/%s/%s', $min, serialize($publicKeys)), now()->addHour(), $callback);
    }

    public function getUsernameByAddress(string $address): ?string
    {
        return $this->get(sprintf('username_by_address/%s', $address));
    }

    public function setUsernameByAddress(string $address, string $username): string
    {
        return $this->remember(sprintf('username_by_address/%s', $address), now()->addHour(), fn () => $username);
    }

    public function getUsernameByPublicKey(string $publicKey): ?string
    {
        return $this->get(sprintf('username_by_public_key/%s', $publicKey));
    }

    public function setUsernameByPublicKey(string $publicKey, string $username): string
    {
        return $this->remember(sprintf('username_by_public_key/%s', $publicKey), now()->addHour(), fn () => $username);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('wallet');
    }
}
