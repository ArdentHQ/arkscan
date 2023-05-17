<?php

declare(strict_types=1);

namespace App\Http\Livewire\Navbar;

use App\Facades\Settings;
use App\Http\Livewire\Concerns\HandlesSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Livewire\Component;

/* Ignore phpstan error for final or abstract class, as we can use this toggle component as a generic setting toggle.
/* @phpstan-ignore-next-line */
class Toggle extends Component
{
    use HandlesSettings;

    public string $activeIcon;

    public string $inactiveIcon;

    public string $setting;

    public mixed $activeValue;

    public mixed $inactiveValue;

    public bool $mobile = false;

    public mixed $currentValue = null;

    public function mount(
        string $activeIcon,
        string $inactiveIcon,
        string $setting,
        mixed $activeValue = true,
        mixed $inactiveValue = false,
        mixed $mobile = false,
    ): void {
        $this->activeIcon    = $activeIcon;
        $this->inactiveIcon  = $inactiveIcon;
        $this->setting       = $setting;
        $this->activeValue   = $activeValue;
        $this->inactiveValue = $inactiveValue;
        $this->mobile        = $mobile;

        $this->currentValue = Settings::get($this->setting);
    }

    public function render(): View
    {
        if ($this->mobile) {
            return view('livewire.navbar.mobile-toggle');
        }

        return view('livewire.navbar.toggle');
    }

    /**
     * @return void|RedirectResponse
     */
    public function toggle()
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

    public function isActive(): bool
    {
        return $this->currentValue === $this->activeValue;
    }

    protected function save(): void
    {
        $this->saveSetting($this->setting, $this->currentValue);
    }
}
