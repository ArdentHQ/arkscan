<?php

declare(strict_types=1);

namespace Tests\Stubs;

use App\Console\Commands\Concerns\DispatchesStatisticsEvents;
use App\Events\NewBlock;
use App\Http\Controllers\Inertia\Concerns\WithFilters;

class WithFiltersStub
{
    use WithFilters;

    public const FILTERS = [
        'test-group' => [
            'filter_one' => true,
            'filter_two' => false,
        ],
    ];

    public function testHasFilter(string $name, bool $defaultValue): bool
    {
        return $this->hasFilter($name, $defaultValue);
    }

    public function testDefaultFilters(?string $group = null): array
    {
        return $this->defaultFilters($group);
    }

    public function testFilters(?string $group = null): array
    {
        return $this->filters($group);
    }

    public function testHasFilters(?string $group = null): bool
    {
        return $this->hasFilters($group);
    }
}
