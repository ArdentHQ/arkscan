<?php

declare(strict_types=1);

use App\Http\Livewire\Navbar\MobileDarkModeToggle;
use Illuminate\Support\Facades\Cookie;
use Livewire\ComponentChecksumManager;
use Livewire\Livewire;

beforeEach(function () {
    $this->options   = [
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
        'options' => $this->options,
        'setting' => 'theme',
    ]);
});

it('should render', function () {
    $this->component->assertSet('options', $this->options)
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

    $this->component->emit('themeChanged', 'dark')
        ->assertNotDispatchedBrowserEvent('setThemeMode')
        ->emit('themeChanged', 'dark')
        ->assertNotDispatchedBrowserEvent('setThemeMode');
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
                'options'      => $this->options,
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
