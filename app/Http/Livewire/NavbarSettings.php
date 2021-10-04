<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Services\Settings;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

final class NavbarSettings extends Component
{
    public array $state = [];

    public function mount(): void
    {
        $this->state = Settings::all();
    }

    public function updatedState(): void
    {
        $originalCurrency = Arr::get(Settings::all(), 'currency');
        $newCurrency      = Arr::get($this->state, 'currency');

        $originalTheme = Arr::get(Settings::all(), 'darkTheme');
        $newTheme      = Arr::get($this->state, 'darkTheme');

        Session::put('settings', json_encode($this->state));

        if ($originalCurrency !== $newCurrency) {
            $this->emit('currencyChanged', $newCurrency);
        }

        if ($originalTheme !== $newTheme) {
            $this->emit('toggleDarkMode', Settings::theme());
        }
    }
}
