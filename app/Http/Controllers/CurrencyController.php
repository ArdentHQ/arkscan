<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\Settings;
use App\Http\Livewire\Concerns\HandlesSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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

            // $this->dispatch('currencyChanged', $newCurrency);
        }

        return redirect()->back();
    }
}
