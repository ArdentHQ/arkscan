<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class PriceChartCache implements Contract
{
    use Concerns\ManagesCache;
    use Concerns\ManagesChart;

    public function getDay(string $currency): array
    {
        return $this->get("day/$currency", []);
    }

    public function setDay(string $currency, Collection $data): array
    {
        return $this->remember("day/$currency", now()->addHour(), fn () => $this->chartjs($data));
    }

    public function getWeek(string $currency): array
    {
        return $this->get("week/$currency", []);
    }

    public function setWeek(string $currency, Collection $data): array
    {
        return $this->remember("week/$currency", now()->addHour(), fn () => $this->chartjs($data));
    }

    public function getMonth(string $currency): array
    {
        return $this->get("month/$currency", []);
    }

    public function setMonth(string $currency, Collection $data): array
    {
        return $this->remember("month/$currency", now()->addHour(), fn () => $this->chartjs($data));
    }

    public function getQuarter(string $currency): array
    {
        return $this->get("quarter/$currency", []);
    }

    public function setQuarter(string $currency, Collection $data): array
    {
        return $this->remember("quarter/$currency", now()->addHour(), fn () => $this->chartjs($data));
    }

    public function getYear(string $currency): array
    {
        return $this->get("year/$currency", []);
    }

    public function setYear(string $currency, Collection $data): array
    {
        return $this->remember("year/$currency", now()->addHour(), fn () => $this->chartjs($data));
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('price_chart');
    }
}
