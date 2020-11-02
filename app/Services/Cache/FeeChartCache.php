<?php

declare(strict_types=1);

namespace App\Services\Cache;

use App\Contracts\Cache as Contract;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class FeeChartCache implements Contract
{
    use Concerns\ManagesCache;
    use Concerns\ManagesChart;

    public function getDay(): array
    {
        return $this->get('day');
    }

    public function setDay(Collection $data): void
    {
        $this->put('day', $this->chartjs($data));
    }

    public function getWeek(): array
    {
        return $this->get('week');
    }

    public function setWeek(Collection $data): void
    {
        $this->put('week', $this->chartjs($data));
    }

    public function getMonth(): array
    {
        return $this->get('month');
    }

    public function setMonth(Collection $data): void
    {
        $this->put('month', $this->chartjs($data));
    }

    public function getQuarter(): array
    {
        return $this->get('quarter');
    }

    public function setQuarter(Collection $data): void
    {
        $this->put('quarter', $this->chartjs($data));
    }

    public function getYear(): array
    {
        return $this->get('year');
    }

    public function setYear(Collection $data): void
    {
        $this->put('year', $this->chartjs($data));
    }

    public function getCache(): TaggedCache
    {
        return Cache::tags('fee_chart');
    }
}
