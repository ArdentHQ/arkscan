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

    public function low(): BigNumber
    {
        return $this->getFee('min');
    }

    public function average(): BigNumber
    {
        return $this->getFee('avg');
    }

    public function high(): BigNumber
    {
        return $this->getFee('max');
    }

    private function getFee(string $name): BigNumber
    {
        $fee = Arr::get($this->cache->getFees(), $name);
        if ($fee === null) {
            return BigNumber::zero();
        }

        return BigNumber::new(UnitConverter::formatUnits($fee, 'gwei'));
    }
}
