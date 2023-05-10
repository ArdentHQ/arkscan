<?php

declare(strict_types=1);

namespace App\Http\Livewire\Navbar;

use App\Facades\Settings;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;
use Livewire\Component;

/* Ignore phpstan error for final or abstract class, as we can use this toggle component as a generic setting toggle.
/* @phpstan-ignore-next-line */
class Toggle extends Component
{
    public string $activeIcon;

    public string $inactiveIcon;

    public string $setting;

    public mixed $activeValue;

    public mixed $inactiveValue;

    public mixed $currentValue = null;

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

        $this->currentValue = Settings::get($this->setting);
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

    protected function save(): void
    {
        $settings = Settings::all();
        $settings[$this->setting] = $this->currentValue;

        Cookie::queue('settings', json_encode($settings), 60 * 24 * 365 * 5);
    }

    protected function isActive(): bool
    {
        return $this->currentValue === $this->activeValue;
    }
}
