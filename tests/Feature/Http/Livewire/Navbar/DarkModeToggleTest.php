<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Http\Livewire\Navbar\DarkModeToggle;
use Illuminate\Support\Facades\Cookie;
use Livewire\Livewire;

beforeEach(function () {
    $this->component = Livewire::test(DarkModeToggle::class, [
        'activeIcon'   => 'underline-moon',
        'inactiveIcon' => 'underline-sun',
        'setting'      => 'darkTheme',
    ]);
});

it('should render', function () {
    $this->component->assertSet('activeIcon', 'underline-moon')
        ->assertSet('inactiveIcon', 'underline-sun')
        ->assertSet('setting', 'darkTheme')
        ->assertSet('activeValue', true)
        ->assertSet('inactiveValue', false)
        ->assertSet('value', false)
        ->assertSee('svg');
});

it('should get icon', function () {
    $instance = $this->component->instance();

    expect($instance->icon())->toBe('underline-sun');

    $instance->toggle();

    expect($instance->icon())->toBe('underline-moon');
});

it('should toggle and save settings', function () {
    Cookie::shouldReceive('queue')
        ->with('settings', json_encode([
            ...Settings::all(),
            'darkTheme' => true,
        ]), 60 * 24 * 365 * 5)
        ->once();

    Cookie::shouldReceive('queue')
        ->with('settings', json_encode([
            ...Settings::all(),
            'darkTheme' => false,
        ]), 60 * 24 * 365 * 5)
        ->once();

    $this->component->assertSet('activeIcon', 'underline-moon')
        ->assertSet('inactiveIcon', 'underline-sun')
        ->assertSet('setting', 'darkTheme')
        ->assertSet('currentValue', false)
        ->call('toggle')
        ->assertDispatchedBrowserEvent('setThemeMode', [
            'theme' => 'dark',
        ])
        ->call('toggle')
        ->assertDispatchedBrowserEvent('setThemeMode', [
            'theme' => 'light',
        ]);
});
