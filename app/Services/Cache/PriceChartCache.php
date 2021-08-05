<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use App\Services\Cache\Concerns\ManagesCache;
use App\Services\Cache\Concerns\ManagesChart;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class PriceChartCache implements Contract
{
    use ManagesCache;
    use ManagesChart;

    public function getHistorical(string $currency, string $period): array
    {
        return $this->get(sprintf('historical/%s/%s', $currency, $period), []);
    }

    public function setHistorical(string $currency, string $period, Collection $data): void
    {
        $this->put(sprintf('historical/%s/%s', $currency, $period), $this->chartjs($data));
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('price_chart');
    }
}
