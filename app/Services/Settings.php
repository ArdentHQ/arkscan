<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

/** @phpstan-ignore-next-line */
class Settings
{
    public function all(): array
    {
        $defaultSettings = [
            'currency'       => 'USD',
            'priceChart'     => true,
            'feeChart'       => true,
            'darkTheme'      => null,
            'expandedTables' => false,
        ];

        if (Cookie::has('settings')) {
            $sessionSettings = json_decode(strval(Cookie::get('settings')), true);

            return $sessionSettings + $defaultSettings;
        }

        return $defaultSettings;
    }

    public function currency(): string
    {
        return Str::upper(Arr::get($this->all(), 'currency'));
    }

    public function locale(): string
    {
        // Since sometimes the key exists (crypto values) but returns `null`, we need another default value
        // in the end to handle that case
        return Arr::get(config('currencies'), strtolower($this->currency()).'.locale', 'en_US') ?? 'en_US';
    }

    public function priceChart(): bool
    {
        return (bool) Arr::get($this->all(), 'priceChart', true);
    }

    public function feeChart(): bool
    {
        return (bool) Arr::get($this->all(), 'feeChart', true);
    }

    public function theme(): string
    {
        if (Arr::get($this->all(), 'darkTheme') === true) {
            return 'dark';
        } elseif (Arr::get($this->all(), 'darkTheme') === false) {
            return 'light';
        }

        return 'auto';
    }

    public function expandedTables(): bool
    {
        return (bool) Arr::get($this->all(), 'expandedTables', false);
    }

    public function usesCharts(): bool
    {
        return $this->usesPriceChart() || $this->usesFeeChart();
    }

    public function usesPriceChart(): bool
    {
        if (config('explorer.network') !== 'production') {
            return false;
        }

        return $this->priceChart();
    }

    public function usesFeeChart(): bool
    {
        return $this->feeChart();
    }

    public function usesExpandedTables(): bool
    {
        return $this->expandedTables();
    }
}
