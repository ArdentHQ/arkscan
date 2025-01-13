<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class PriceCache implements Contract
{
    use ManagesCache;

    public function getLastUpdated(): array
    {
        return $this->get('last_updated', []);
    }

    public function setLastUpdated(array $data): void
    {
        $this->put('last_updated', $data);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('price');
    }
}
