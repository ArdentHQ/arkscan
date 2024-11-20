<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class MainsailCache implements Contract
{
    use ManagesCache;

    public function getFees(): array
    {
        return $this->get('fees', []);
    }

    public function setFees(array $data): void
    {
        $this->put('fees', $data);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('mainsail');
    }
}
