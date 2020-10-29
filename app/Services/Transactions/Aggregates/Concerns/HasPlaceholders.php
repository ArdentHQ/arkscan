<?php

declare(strict_types=1);

namespace App\Services\Transactions\Aggregates\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait HasPlaceholders
{
    private function mergeWithPlaceholders(Collection $aggregate, Collection $placeholders): Collection
    {
        $result = [];

        foreach ($placeholders as $placeholder) {
            $result[$placeholder] = Arr::get($aggregate, $placeholder, 0);
        }

        return collect($result);
    }

    private function placeholders(int $start, int $end, int $step, string $format): Collection
    {
        $times = [];

        foreach (range($start, $end, $step) as $timestamp) {
            $times[] = gmdate($format, $timestamp);
        }

        return collect(array_combine($times, $times));
    }
}
