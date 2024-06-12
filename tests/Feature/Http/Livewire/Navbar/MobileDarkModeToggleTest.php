<?php

declare(strict_types=1);

use App\Http\Livewire\Navbar\MobileDarkModeToggle;
use Illuminate\Support\Facades\Cookie;
use Livewire\ComponentChecksumManager;
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
    $payload = [
        'fingerprint' => [
            'id'     => 'rYrH6NyxlBPbUP3uqMGk',
            'name'   => 'navbar.mobile-dark-mode-toggle',
            'locale' => 'en',
            'path'   => 'invalid-route-path',
            'method' => 'GET',
            'v'      => 'acj',
        ],
        'serverMemo' => [
            'children' => [],
            'errors'   => [],
            'htmlHash' => '19fb4fd4',
            'data'     => [
                'options'      => $this->themeOptions,
                'setting'      => 'theme',
                'currentValue' => 'light',
            ],
            'dataMeta' => [],
        ],
        'updates' => [
            [
                'type'    => 'fireEvent',
                'payload' => [
                    'id'     => '5i31j',
                    'event'  => 'themeChanged',
                    'params' => ['dark'],
                ],
            ],
        ],
    ];

    $payload['serverMemo']['checksum'] = (new ComponentChecksumManager())->generate($payload['fingerprint'], $payload['serverMemo']);

    $this->post('/livewire/message/navbar.mobile-dark-mode-toggle', $payload)
        ->assertOk();
});
