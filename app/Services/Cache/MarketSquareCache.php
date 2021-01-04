<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class MarketSquareCache implements Contract
{
    use ManagesCache;

    public function getProfile(string $address): array
    {
        return $this->get(sprintf('profile/%s', $address), []);
    }

    public function setProfile(string $address, array $value): void
    {
        $this->put(sprintf('profile/%s', $address), $value);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('marketsquare');
    }
}
