<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Services\GasTracker as Contract;
use App\Services\Cache\MainsailCache;
use ArkEcosystem\Crypto\Utils\UnitConverter;
use Illuminate\Support\Arr;

final class GasTracker implements Contract
{
    private MainsailCache $cache;

    public function __construct()
    {
        $this->cache = new MainsailCache();
    }

    public function low(): float
    {
        return $this->getFee('min');
    }

    public function average(): float
    {
        return $this->getFee('avg');
    }

    public function high(): float
    {
        return $this->getFee('max');
    }

    private function getFee(string $name): float
    {
        $fee = Arr::get($this->cache->getFees(), $name);
        if ($fee === null) {
            return 0;
        }

        return UnitConverter::formatUnits((string) $fee, 'gwei');
    }
}
