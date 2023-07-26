<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Http\Livewire\Navbar\Toggle;
use Illuminate\Support\Facades\Cookie;
use Livewire\Livewire;

beforeEach(function () {
    $this->component = Livewire::test(Toggle::class, [
        'activeIcon'   => 'underline-moon',
        'inactiveIcon' => 'underline-sun',
        'setting'      => 'priceChart',
    ]);
});

it('should render', function () {
    expect(Settings::get('priceChart'))->toBeTrue();

    $this->component->assertSet('activeIcon', 'underline-moon')
        ->assertSet('inactiveIcon', 'underline-sun')
        ->assertSet('setting', 'priceChart')
        ->assertSet('activeValue', true)
        ->assertSet('inactiveValue', false)
        ->assertSet('currentValue', true)
        ->assertSee('svg');
});

it('should display mobile component', function () {
    expect(Settings::get('priceChart'))->toBeTrue();

    Livewire::test(Toggle::class, [
        'activeIcon'   => 'underline-moon',
        'inactiveIcon' => 'underline-sun',
        'setting'      => 'priceChart',
        'mobile'       => true,
    ])
        ->assertSet('activeIcon', 'underline-moon')
        ->assertSet('inactiveIcon', 'underline-sun')
        ->assertSet('setting', 'priceChart')
        ->assertSet('activeValue', true)
        ->assertSet('inactiveValue', false)
        ->assertSet('mobile', true)
        ->assertSet('currentValue', true)
        ->assertSee('svg');
});

it('should get icon', function () {
    $instance = $this->component->instance();

    expect($instance->icon())->toBe('underline-moon');

    $instance->toggle();

    expect($instance->icon())->toBe('underline-sun');
});

it('should toggle and save settings', function () {
    Cookie::shouldReceive('queue')
        ->with('settings', json_encode([
            ...Settings::all(),
            'priceChart' => false,
        ]), 60 * 24 * 365 * 5)
        ->once();

    Cookie::shouldReceive('queue')
        ->with('settings', json_encode([
            ...Settings::all(),
            'priceChart' => true,
        ]), 60 * 24 * 365 * 5)
        ->once();

    $this->component->assertSet('activeIcon', 'underline-moon')
        ->assertSet('inactiveIcon', 'underline-sun')
        ->assertSet('setting', 'priceChart')
        ->assertSet('currentValue', true)
        ->call('toggle')
        ->call('toggle');
});
