<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates;

use App\Services\Transactions\Aggregates\Concerns\HasQueries;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

final class FeesByDayAggregate
{
    use HasQueries;

    public function aggregate(): Collection
    {
        $hours      = $this->getHoursRange();
        $aggregate  = (new FeeByRangeAggregate())->aggregate(Carbon::now()->subDay(), Carbon::now()->endOfDay(), 'H:i');

        $result = [];
        foreach ($hours as $key) {
            $result[$key] = Arr::get($aggregate, $key, 0);
        }

        return collect($result);
    }

    private function getHoursRange(): array
    {
        $times = [];

        foreach (range(0, 86400, 3600) as $timestamp) {
            $times[] = gmdate('H:i', $timestamp);
        }

        /* @phpstan-ignore-next-line */
        return array_combine($times, $times);
    }
}
