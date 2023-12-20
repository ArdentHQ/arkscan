<?php

declare(strict_types=1);

namespace App\Http\Livewire\Navbar;

use App\Facades\Settings;
use App\Http\Livewire\Concerns\HandlesSettings;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

/* Ignore phpstan error for final or abstract class, as we can use this toggle component as a generic setting toggle.
/* @phpstan-ignore-next-line */
class MobileToggle extends Component
{
    use HandlesSettings;

    public array $options;

    public string $setting;

    public mixed $currentValue = null;

    public function mount(
        array $options,
        string $setting,
    ): void {
        $this->options = $options;
        $this->setting = $setting;

        $this->currentValue = Settings::get($this->setting);
    }

    public function render(): View
    {
        return view('livewire.navbar.mobile-toggle');
    }

    public function setValue(string|int $value): void
    {
        $this->currentValue = $value;

        $this->save();
    }

    public function icon(): string
    {
        return Collection::make($this->options)
            ->firstWhere('value', $this->currentValue)['icon'];
    }

    protected function save(): void
    {
        $this->saveSetting($this->setting, $this->currentValue);
    }
}
