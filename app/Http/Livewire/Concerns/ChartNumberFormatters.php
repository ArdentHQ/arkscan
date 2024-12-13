<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Facades\Network;
use App\Services\BigNumber;
use App\Services\NumberFormatter;

trait ChartNumberFormatters
{
    private function asMoney(string | int | float | BigNumber $value, bool $isGwei = true): string
    {
        if ($value instanceof BigNumber) {
            $value = (string) $value;
        }

        if (! $isGwei) {
            return NumberFormatter::currency($value, Network::currency());
        }

        return NumberFormatter::currency(
            NumberFormatter::gweiToArk($value),
            Network::currency(),
        );
    }

    private function asNumber(string | int | float $value): string
    {
        return NumberFormatter::number($value);
    }
}
