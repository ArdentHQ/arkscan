<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Settings;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;
use Livewire\Component;

final class NavbarSettings extends Component
{
    public array $state = [];

    public function mount(): void
    {
        $this->state = Settings::all();
    }

    public function updatedState(mixed $value, string $key): void
    {
        $originalCurrency = Arr::get(Settings::all(), 'currency');
        $newCurrency      = Arr::get($this->state, 'currency');

        Cookie::queue('settings', json_encode($this->state), 60 * 24 * 365 * 5);

        if ($originalCurrency !== $newCurrency) {
            $this->emit('currencyChanged', $newCurrency);
        }

        if ($key === 'darkTheme') {
            $this->dispatchBrowserEvent('setThemeMode', [
                'theme' => $this->state['darkTheme'] === true ? 'dark' : 'light',
            ]);
        }
    }
}
