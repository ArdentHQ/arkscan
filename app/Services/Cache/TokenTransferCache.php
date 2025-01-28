<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class TokenTransferCache implements Contract
{
    use ManagesCache;

    public function getTokenName(string $transactionId): ?string
    {
        return $this->get('name/'.$transactionId);
    }

    public function setTokenName(string $transactionId, string $tokenName)
    {
        $this->put('name/'.$transactionId, $tokenName);
    }

    public function hasTokenName(string $transactionId): bool
    {
        return $this->has('name/'.$transactionId);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('tokenTransfer');
    }
}
