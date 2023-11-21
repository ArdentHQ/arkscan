<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Enums\StatsPeriods;

trait AvailablePeriods
{
    public string $period = '';

    private function defaultPeriod(): string
    {
        return StatsPeriods::DAY;
    }

    private function availablePeriods(): array
    {
        return [
            StatsPeriods::DAY     => trans('forms.statistics.periods.day'),
            StatsPeriods::WEEK    => trans('forms.statistics.periods.week'),
            StatsPeriods::MONTH   => trans('forms.statistics.periods.month'),
            StatsPeriods::QUARTER => trans('forms.statistics.periods.quarter'),
            StatsPeriods::YEAR    => trans('forms.statistics.periods.year'),
            StatsPeriods::ALL     => trans('forms.statistics.periods.all'),
        ];
    }
}
