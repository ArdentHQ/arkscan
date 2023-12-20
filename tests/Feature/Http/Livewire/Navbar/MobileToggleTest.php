<?php

declare(strict_types=1);

use App\Facades\Settings;
use App\Http\Livewire\Navbar\MobileToggle;
use Illuminate\Support\Facades\Cookie;
use Livewire\Livewire;

beforeEach(function () {
    $this->options   = [
        [
            'icon' => 'sun',
            'value' => 'light',
        ],
        [
            'icon' => 'moon',
            'value' => 'dark',
        ],
        [
            'icon' => 'moon-stars',
            'value' => 'dim',
        ],
    ];

    $this->component = Livewire::test(MobileToggle::class, [
        'options' => $this->options,
        'setting' => 'theme',
    ]);
});

it('should render', function () {
    expect(Settings::get('priceChart'))->toBeTrue();

    $this->component->assertSet('options', $this->options)
        ->assertSet('setting', 'theme')
        ->assertSet('currentValue', null)
        ->assertSee('svg');
});

it('should get icon', function () {
    $instance = $this->component->instance();
    $instance->setValue('dark');

    expect($instance->icon())->toBe('moon');

    $instance->setValue('light');

    expect($instance->icon())->toBe('sun');
});

it('should save settings', function () {
    Cookie::shouldReceive('queue')
        ->with('settings', json_encode([
            ...Settings::all(),
            'theme' => 'light',
        ]), 60 * 24 * 365 * 5)
        ->once();

    Cookie::shouldReceive('queue')
        ->with('settings', json_encode([
            ...Settings::all(),
            'theme' => 'dark',
        ]), 60 * 24 * 365 * 5)
        ->once();

    Cookie::shouldReceive('queue')
        ->with('settings', json_encode([
            ...Settings::all(),
            'theme' => 'dim',
        ]), 60 * 24 * 365 * 5)
        ->once();

    $this->component->call('setValue', 'light')
        ->call('setValue', 'dark')
        ->call('setValue', 'dim');
});
