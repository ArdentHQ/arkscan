<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\Settings;
use App\Http\Livewire\Concerns\HandlesSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class ThemeController
{
    use HandlesSettings;

    public function update(Request $request): RedirectResponse
    {
        $newTheme = $request->input('theme');

        $originalTheme = Settings::theme();
        
        if ($originalTheme !== $newTheme) {
            $this->saveSetting('theme', $newTheme);
        }

        return redirect()->back();
    }
}
