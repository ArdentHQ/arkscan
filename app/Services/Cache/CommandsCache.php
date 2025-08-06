<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class CommandsCache implements Contract
{
    use ManagesCache;

    public function getPricesLastUpdated(): array
    {
        return $this->get('price:last_updated', []);
    }

    public function setPricesLastUpdated(array $data): void
    {
        $this->put('price:last_updated', $data);
    }

    public function getResignationIdsLastUpdated(): int
    {
        return $this->get('resignation_ids:last_updated', 0);
    }

    public function setResignationIdsLastUpdated(int $data): void
    {
        $this->put('resignation_ids:last_updated', $data);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('commands');
    }
}
