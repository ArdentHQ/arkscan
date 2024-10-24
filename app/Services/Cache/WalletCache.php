<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Models\Wallet;
use App\Services\Cache\Concerns\ManagesCache;
use Closure;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class WalletCache implements Contract
{
    use ManagesCache;

    public function getKnown(): array
    {
        return $this->get('known', []);
    }

    public function setKnown(Closure $callback): array
    {
        return $this->remember('known', now()->addDay(), $callback);
    }

    public function getLastBlock(string $address): array
    {
        return $this->get(sprintf('last_block/%s', $address), []);
    }

    public function setLastBlock(string $address, array $blocks): void
    {
        $this->put(sprintf('last_block/%s', $address), $blocks);
    }

    public function getPerformance(string $address): array
    {
        return $this->get(sprintf('performance/%s', $address), [true, true]);
    }

    public function setPerformance(string $address, array $value): void
    {
        $this->put(sprintf('performance/%s', $address), $value);
    }

    public function getProductivity(string $address): float
    {
        return (float) $this->get(sprintf('productivity/%s', $address), -1);
    }

    public function setProductivity(string $address, float $value): void
    {
        $this->put(sprintf('productivity/%s', $address), $value);
    }

    public function getResignationId(string $address): ?string
    {
        return $this->get(sprintf('resignation_id/%s', $address));
    }

    public function setResignationId(string $address, string $id): void
    {
        $this->put(sprintf('resignation_id/%s', $address), $id);
    }

    public function getVote(string $address): ?Wallet
    {
        return $this->get(sprintf('vote/%s', $address));
    }

    public function setVote(string $address, Wallet $value): void
    {
        $this->put(sprintf('vote/%s', $address), $value);
    }

    public function getMultiSignatureAddress(int $min, array $publicKeys): ?string
    {
        return $this->get(sprintf('multi_signature/%s/%s', $min, serialize($publicKeys)));
    }

    public function setMultiSignatureAddress(int $min, array $publicKeys, Closure $callback): void
    {
        $this->remember(sprintf('multi_signature/%s/%s', $min, serialize($publicKeys)), now()->addHour(), $callback);
    }

    public function getUsernameByAddress(string $address): ?string
    {
        return $this->get(sprintf('username_by_address/%s', $address));
    }

    public function setUsernameByAddress(string $address, string $username): void
    {
        $this->put(sprintf('username_by_address/%s', $address), $username);
    }

    public function forgetUsernameByAddress(string $address): void
    {
        $this->forget(sprintf('username_by_address/%s', $address));
    }

    public function getValidator(string $address): ?Wallet
    {
        return $this->get(sprintf('validator/%s', $address));
    }

    public function setValidator(string $address, Wallet $wallet): void
    {
        $this->put(sprintf('validator/%s', $address), $wallet);
    }

    public function getVoterCount(string $address): int
    {
        return (int) $this->get(sprintf('voter_count/%s', $address), 0);
    }

    public function setVoterCount(string $address, int $count): void
    {
        $this->put(sprintf('voter_count/%s', $address), $count);
    }

    public function getMissedBlocks(string $address): int
    {
        return (int) $this->get(sprintf('missed_blocks/%s', $address), 0);
    }

    public function setMissedBlocks(string $address, int $value): void
    {
        $this->put(sprintf('missed_blocks/%s', $address), $value);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('wallet');
    }
}
