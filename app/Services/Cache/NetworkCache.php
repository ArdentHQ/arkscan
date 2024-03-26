<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use Closure;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class NetworkCache implements Contract
{
    use ManagesCache;

    public function getHeight(): int
    {
        return (int) $this->get('height');
    }

    public function setHeight(Closure $callback): int
    {
        return (int) $this->remember('height', $this->blockTimeTTL(), $callback);
    }

    public function getSupply(): float
    {
        return (float) $this->get('supply');
    }

    public function setSupply(Closure $callback): float
    {
        return (float) $this->remember('supply', $this->blockTimeTTL(), $callback);
    }

    public function getTotalSupply(): float
    {
        return (float) $this->get('total_supply');
    }

    public function setTotalSupply(Closure $callback): float
    {
        return (float) $this->remember('total_supply', $this->blockTimeTTL(), $callback);
    }

    public function getVolume(): float
    {
        return (int) $this->get('volume');
    }

    public function setVolume(string $value): void
    {
        $this->put('volume', $value);
    }

    public function getTransactionsCount(): int
    {
        return (int) $this->get('transactions_count');
    }

    public function setTransactionsCount(string $value): void
    {
        $this->put('transactions_count', $value);
    }

    public function getVotesCount(): int
    {
        return (int) $this->get('votes_count');
    }

    public function setVotesCount(string $value): void
    {
        $this->put('votes_count', $value);
    }

    public function getVotesPercentage(): float
    {
        return (float) $this->get('votes_percentage');
    }

    public function setVotesPercentage(string $value): void
    {
        $this->put('votes_percentage', $value);
    }

    public function getValidatorRegistrationCount(): int
    {
        return (int) $this->get('validator_registration_count');
    }

    public function setValidatorRegistrationCount(int $value): void
    {
        $this->put('validator_registration_count', $value);
    }

    public function getFeesCollected(): float
    {
        return (float) $this->get('fees_collected');
    }

    public function setFeesCollected(string $value): void
    {
        $this->put('fees_collected', $value);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('network');
    }
}
