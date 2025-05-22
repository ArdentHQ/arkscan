<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Services\NumberFormatter;

trait ChartNumberFormatters
{
    private function asNumber(string | int | float $value): string
    {
        return NumberFormatter::number($value);
    }
}
