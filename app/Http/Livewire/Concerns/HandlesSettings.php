<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Facades\Settings;
use Illuminate\Support\Facades\Cookie;

trait HandlesSettings
{
    private function saveSetting(string $setting, mixed $value): void
    {
        $settings           = Settings::all();
        $settings[$setting] = $value;

        Cookie::queue('settings', json_encode($settings), 60 * 24 * 365 * 5);
    }
}
