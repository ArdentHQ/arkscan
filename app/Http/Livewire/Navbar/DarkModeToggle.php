<?php

declare(strict_types=1);

namespace App\Http\Livewire\Navbar;

final class DarkModeToggle extends Toggle
{
    /** @var mixed */
    protected $listeners = [
        'themeChanged' => 'storeTheme',
    ];

    protected function save(bool $dispatchEvent = true): void
    {
        parent::save();

        if ($dispatchEvent && $this->setting === 'darkTheme') {
            $this->dispatchBrowserEvent('setThemeMode', [
                'theme' => $this->isActive() ? 'dark' : 'light',
            ]);
        }
    }

    public function storeTheme(string $theme): void
    {
        if ($this->currentValue !== null) {
            return;
        }

        $this->currentValue = $theme === 'dark';

        $this->save(false);
    }
}
