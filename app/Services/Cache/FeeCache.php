<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
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

    public function getMinimum(string $period): float
    {
        return (float) $this->get(sprintf('minimum/%s', $period));
    }

    public function setMinimum(string $period, float $data): void
    {
        $this->put(sprintf('minimum/%s', $period), $data);
    }

    public function getAverage(string $period): float
    {
        return (float) $this->get(sprintf('average/%s', $period));
    }

    public function setAverage(string $period, float $data): void
    {
        $this->put(sprintf('average/%s', $period), $data);
    }

    public function getMaximum(string $period): float
    {
        return (float) $this->get(sprintf('maximum/%s', $period));
    }

    public function setMaximum(string $period, float $data): void
    {
        $this->put(sprintf('maximum/%s', $period), $data);
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('fee');
    }
}
