<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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
        ];

        $settings = Cookie::get('settings');
        if (Cookie::has('settings') && ! is_array($settings)) {
            $sessionSettings = json_decode(strval($settings), true);

            return $sessionSettings + $defaultSettings;
        }

        return $defaultSettings;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->all(), $key, $default);
    }

    public function currency(): string
    {
        return Str::upper($this->get('currency'));
    }

    public function locale(): string
    {
        // Since sometimes the key exists (crypto values) but returns `null`, we need another default value
        // in the end to handle that case
        return Arr::get(config('currencies'), strtolower($this->currency()).'.locale', 'en_US') ?? 'en_US';
    }

    public function priceChart(): bool
    {
        return (bool) $this->get('priceChart', true);
    }

    public function feeChart(): bool
    {
        return (bool) $this->get('feeChart', true);
    }

    public function theme(): string
    {
        $darkMode = $this->get('darkTheme');
        if ($darkMode === true) {
            return 'dark';
        } elseif ($darkMode === false) {
            return 'light';
        }

        return 'auto';
    }

    public function usesCharts(): bool
    {
        return $this->usesPriceChart() || $this->usesFeeChart();
    }

    public function usesPriceChart(): bool
    {
        if (config('arkscan.network') !== 'production') {
            return false;
        }

        return $this->priceChart();
    }

    public function usesFeeChart(): bool
    {
        return $this->feeChart();
    }
}
