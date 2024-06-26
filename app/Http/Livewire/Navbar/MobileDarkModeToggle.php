<?php

declare(strict_types=1);

namespace App\Http\Livewire\Navbar;

final class MobileDarkModeToggle extends MobileToggle
{
    /** @var mixed */
    protected $listeners = [
        'themeChanged' => 'storeTheme',
    ];

    public function storeTheme(string $newValue): void
    {
        if ($newValue !== $this->currentValue) {
            $this->currentValue = $newValue;

            $this->save(false);

            $this->dispatch('themeChanged', newValue: $newValue);
        }
    }

    protected function save(bool $dispatchEvent = true): void
    {
        parent::save();

        if ($dispatchEvent) {
            $this->dispatch('setThemeMode', [
                'theme' => $this->currentValue,
            ]);
        }
    }
}
