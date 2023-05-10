<?php

namespace App\Http\Livewire\Navbar;

use App\Facades\Settings;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;
use Livewire\Component;

class Toggle extends Component
{
    public string $activeIcon;

    public string $inactiveIcon;

    public string $setting;

    public mixed $activeValue;

    public mixed $inactiveValue;

    public mixed $currentValue;

    public function mount(
        string $activeIcon,
        string $inactiveIcon,
        string $setting,
        mixed $activeValue = true,
        mixed $inactiveValue = false,
    ): void
    {
        $this->activeIcon = $activeIcon;
        $this->inactiveIcon = $inactiveIcon;
        $this->setting = $setting;
        $this->activeValue = $activeValue;
        $this->inactiveValue = $inactiveValue;
    }

    public function render(): View
    {
        return view('livewire.navbar.toggle');
    }

    public function toggle(): void
    {
        if ($this->isActive()) {
            $this->currentValue = $this->inactiveValue;
        } else {
            $this->currentValue = $this->activeValue;
        }

        $this->save();
    }

    public function icon(): string
    {
        if ($this->isActive()) {
            return $this->activeIcon;
        }

        return $this->inactiveIcon;
    }

    private function save(): void
    {
        $settings = Settings::all();
        $settings[$this->setting] = $this->value();

        Cookie::queue('settings', json_encode($settings), 60 * 24 * 365 * 5);

        if ($this->setting === 'darkTheme') {
            $this->dispatchBrowserEvent('setThemeMode', [
                'theme' => $this->isActive() ? 'dark' : 'light',
            ]);
        }
    }

    private function value(): mixed
    {
        if (! isset($this->currentValue)) {
            $this->currentValue = Settings::get($this->setting);
        }

        return $this->currentValue;
    }

    private function isActive(): bool
    {
        return $this->value() === $this->activeValue;
    }
}
