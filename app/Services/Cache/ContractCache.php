<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Facades\Cache;

final class ContractCache implements Contract
{
    use ManagesCache;

    public function getTokenName(string $contractAddress): ?string
    {
        return $this->get('name/'.$contractAddress);
    }

    public function setTokenName(string $contractAddress, string $tokenName): void
    {
        $this->put('name/'.$contractAddress, $tokenName);
    }

    public function hasTokenName(string $contractAddress): bool
    {
        return $this->has('name/'.$contractAddress);
    }

    public function getIsContract(string $contractAddress): bool
    {
        // dump($this->get('is_contract/'.$contractAddress), $contractAddress);
        return $this->get('is_contract/'.$contractAddress) === true;
    }

    public function setIsContract(string $contractAddress, bool $isContract): void
    {
        $this->put('is_contract/'.$contractAddress, $isContract);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('tokenTransfer');
    }
}
