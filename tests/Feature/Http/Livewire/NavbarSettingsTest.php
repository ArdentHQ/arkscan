<?php

declare(strict_types=1);

use App\Http\Livewire\NavbarSettings;
use Livewire\Livewire;

it('should update the currency', function () {
    Livewire::test(NavbarSettings::class)
        ->assertSet('state.currency', 'USD')
        ->set('state.currency', 'CHF')
        ->assertSet('state.currency', 'CHF')
        ->assertEmitted('currencyChanged', 'CHF');
});

it('should update the theme', function () {
    Livewire::test(NavbarSettings::class)
        ->assertSet('state.darkTheme', false)
        ->set('state.darkTheme', true)
        ->assertSet('state.darkTheme', true)
        ->assertDispatchedBrowserEvent('setThemeMode', ['theme' => 'dark']);
});
