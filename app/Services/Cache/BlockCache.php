<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class BlockCache implements Contract
{
    use ManagesCache;

    public function getLargestIdByFees(): ?string
    {
        return $this->get('largest/fees');
    }

    public function setLargestIdByFees(string $id): void
    {
        $this->put('largest/fees', $id);
    }

    public function getLargestIdByTransactionCount(): ?string
    {
        return $this->get('largest/transactions');
    }

    public function setLargestIdByTransactionCount(string $id): void
    {
        $this->put('largest/transactions', $id);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('block');
    }
}
