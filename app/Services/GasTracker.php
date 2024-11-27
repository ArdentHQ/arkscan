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

    public function low() //: int
    {
        return $this->getFee('min');
    }

    public function average() //: int
    {
        return $this->getFee('avg');
    }

    public function high() //: int
    {
        return $this->getFee('max');
    }

    private function getFee(string $name) //: int
    {
        // return BigNumber::new($this->cache->getFees()[$name] ?? 0);
        // return Arr::get($this->cache->getFees(), $name, 0);

        // dd($this->cache->getFees());
        $fee = Arr::get($this->cache->getFees(), $name);
        if ($fee === null) {
            return 0;
        }

        $fee = BigNumber::new($fee);

        return UnitConverter::formatUnits((string) $fee, 'ark');

        return BigNumber::new(UnitConverter::formatUnits($fee, 'ark'));

        dump(UnitConverter::formatUnits($fee, 'ark'));

        return UnitConverter::formatUnits($fee, 'ark');
    }
}
