<?php

declare(strict_types=1);

namespace App\Http\Livewire\Navbar;

final class DarkModeToggle extends Toggle
{
    /** @var mixed */
    protected $listeners = [
        'themeChanged' => 'storeTheme',
    ];

    public function storeTheme(string $theme): void
    {
        $currentValue = $theme === 'dark';

        if ($currentValue !== $this->currentValue) {
            $this->currentValue = $currentValue;

            $this->save(false);

            $this->emit('themeChanged', $theme);
        }
    }

    protected function save(bool $dispatchEvent = true): void
    {
        parent::save();

        if ($dispatchEvent && $this->setting === 'darkTheme') {
            $this->dispatchBrowserEvent('setThemeMode', [
                'theme' => $this->isActive() ? 'dark' : 'light',
            ]);
        }
    }
}
