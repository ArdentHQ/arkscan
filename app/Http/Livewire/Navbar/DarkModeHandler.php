<?php

declare(strict_types=1);

namespace App\Http\Livewire\Navbar;

use App\Facades\Settings;
use App\Http\Livewire\Concerns\HandlesSettings;
use Livewire\Component;

final class DarkModeHandler extends Component
{
    use HandlesSettings;

    public string $setting;

    public mixed $currentValue = null;

    /** @var mixed */
    protected $listeners = [
        'themeChanged' => 'storeTheme',
    ];

    public function mount(string $setting): void {
        $this->setting      = $setting;
        $this->currentValue = Settings::get($this->setting);
    }

    public function render(): string
    {
        return '<div></div>';
    }

    public function storeTheme(string $newValue): void
    {
        if (! in_array($newValue, ['light', 'dark', 'dim'])) {
            return;
        }

        if ($newValue !== $this->currentValue) {
            $this->currentValue = $newValue;

            $this->save(false);

            $this->emit('themeChanged', $newValue);
        }
    }

    protected function save(bool $dispatchEvent = true): void
    {
        $this->saveSetting($this->setting, $this->currentValue);

        if ($dispatchEvent && $this->setting === 'theme') {
            $this->dispatchBrowserEvent('setThemeMode', [
                'theme' => $this->isActive() ? 'dark' : 'light',
            ]);
        }
    }
}
