<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\BigNumber;
use App\Services\Cache\Concerns\ManagesCache;
use App\Services\Cache\Concerns\ManagesChart;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class FeeCache implements Contract
{
    use ManagesCache;
    use ManagesChart;

    public function all(string $period): array
    {
        return [
            'historical' => $this->getHistorical($period),
            'min'        => $this->getMinimum($period),
            'avg'        => $this->getAverage($period),
            'max'        => $this->getMaximum($period),
        ];
    }

    public function getHistorical(string $period): array
    {
        return $this->get(sprintf('historical/%s', $period), []);
    }

    public function setHistorical(string $period, Collection $data): void
    {
        $this->put(sprintf('historical/%s', $period), $this->chartjs($data));
    }

    public function getMinimum(string $key): BigNumber
    {
        $value = $this->get(sprintf('minimum/%s', $key));
        if ($value === null) {
            return BigNumber::zero();
        }

        return BigNumber::new($value);
    }

    public function setMinimum(string $key, string | float $data): void
    {
        $this->put(sprintf('minimum/%s', $key), $data);
    }

    public function getAverage(string $key): BigNumber
    {
        $value = $this->get(sprintf('average/%s', $key));
        if ($value === null) {
            return BigNumber::zero();
        }

        return BigNumber::new($value);
    }

    public function setAverage(string $key, string | float $data): void
    {
        $this->put(sprintf('average/%s', $key), $data);
    }

    public function getMaximum(string $key): BigNumber
    {
        $value = $this->get(sprintf('maximum/%s', $key));
        if ($value === null) {
            return BigNumber::zero();
        }

        return BigNumber::new($value);
    }

    public function setMaximum(string $key, string | float $data): void
    {
        $this->put(sprintf('maximum/%s', $key), $data);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('fee');
    }
}
