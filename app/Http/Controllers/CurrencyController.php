<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\Settings;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Livewire\Concerns\HandlesSettings;

final class CurrencyController
{
    use HandlesSettings;

    public function update(Request $request): RedirectResponse
    {
        $newCurrency = $request->input('currency');

        $originalCurrency = Settings::currency();
        $newCurrency      = Str::upper($newCurrency);

        if ($originalCurrency !== $newCurrency) {
            $this->saveSetting('currency', $newCurrency);
        }

        return redirect()->back();
    }
}
