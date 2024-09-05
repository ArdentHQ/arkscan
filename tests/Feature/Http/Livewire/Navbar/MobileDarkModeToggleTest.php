<?php

declare(strict_types=1);

use App\Http\Livewire\Navbar\MobileDarkModeToggle;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

beforeEach(function () {
    $this->themeOptions   = [
        [
            'icon'  => 'sun',
            'value' => 'light',
        ],
        [
            'icon'  => 'moon',
            'value' => 'dark',
        ],
        [
            'icon'  => 'moon-stars',
            'value' => 'dim',
        ],
    ];

    $this->component = Livewire::test(MobileDarkModeToggle::class, [
        'options' => $this->themeOptions,
        'setting' => 'theme',
    ]);
});

it('should render', function () {
    $this->component->assertSet('options', $this->themeOptions)
        ->assertSet('setting', 'theme')
        ->assertSet('currentValue', null)
        ->assertSee('svg');
});

it('should get icon', function () {
    $instance = $this->component
        ->call('storeTheme', 'light')
        ->instance();

    expect($instance->icon())->toBe('sun');

    $instance->storeTheme('dark');

    expect($instance->icon())->toBe('moon');

    $instance->storeTheme('dim');

    expect($instance->icon())->toBe('moon-stars');
});

it('should store theme from an event only on first load', function () {
    Cookie::shouldReceive('queue')
        ->once();

    $this->component->dispatch('themeChanged', 'dark')
        ->assertNotDispatched('setThemeMode')
        ->dispatch('themeChanged', 'dark')
        ->assertNotDispatched('setThemeMode');
});

it('should dispatch event on save', function () {
    Cookie::shouldReceive('queue')
        ->once();

    $this->component->call('setValue', 'dark')
        ->assertDispatched('setThemeMode', [
            'theme' => 'dark',
        ]);
});

it('should handle 404 and not spam livewire requests', function () {
    Livewire::setUpdateRoute(function ($handle) {
        return Route::post('/livewire/update', $handle);
    });

    $payload = [
        'components' => [
            [
                'snapshot' => json_encode([
                    'data' => [
                        'options' => [
                            [
                                [
                                    [
                                        'icon'  => 'sun',
                                        'value' => 'light',
                                    ],
                                    ['s' => 'arr'],
                                ],
                                [
                                    [
                                        'icon'  => 'moon',
                                        'value' => 'dark',
                                    ],
                                    ['s' => 'arr'],
                                ],
                                [
                                    [
                                        'icon'  => 'moon-stars',
                                        'value' => 'dim',
                                    ],
                                    ['s' => 'arr'],
                                ],
                            ],
                            ['s' => 'arr'],
                        ],
                        'setting' => 'theme',
                        'currentValue' => 'dark'
                    ],
                    'memo' => [
                        'id'      => 'x379QXjQDbJVacXZUrKA',
                        'name'    => 'navbar.mobile-dark-mode-toggle',
                        'path'    => 'delegates',
                        'method'  => 'GET',
                        'children' => [],
                        'scripts' => [],
                        'assets'  => [],
                        'errors'  => [],
                        'locale'  => 'en',
                    ],
                    'checksum' => 'd0d8b6bf20ba442262d305eff05183949e6510c7be8a0ad11311fa92db4a5739',
                ]),
                'updates'  => [],
                'calls'    => [
                    [
                        'path'   => 'invalid-route-path',
                        'method' => '__dispatch',
                        'params' => ['themeChanged', ['newValue' => 'dark']],
                    ],
                ],
            ],
        ],
        '_token' => '123',
    ];

    $this->post('/livewire/update', $payload)
        ->assertOk();
});
