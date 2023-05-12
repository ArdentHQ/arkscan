<?php

declare(strict_types=1);

use App\Http\Livewire\Navbar\NetworkToggle;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

beforeEach(function () {
    $this->component = Livewire::test(NetworkToggle::class, [
        'activeIcon'   => 'app-ark-testnet',
        'inactiveIcon' => 'networks.ark',
        'setting'      => 'network',
    ]);
});

it('should render', function () {
    $this->component->assertSet('activeIcon', 'app-ark-testnet')
        ->assertSet('inactiveIcon', 'networks.ark')
        ->assertSet('setting', 'network')
        ->assertSet('activeValue', true)
        ->assertSet('inactiveValue', false)
        ->assertSet('currentValue', false)
        ->assertSee('svg');
});

it('should check is active based on current network', function (string $network, bool $expected) {
    Config::set('explorer.network', $network);

    expect($this->component->instance()->isActive())->toBe($expected);
})->with([
    'mainnet' => ['production', false],
    'testnet' => ['development', true],
]);

it('should redirect to the correct network on toggle', function (string $network, string $expected) {
    Config::set('explorer.network', $network);

    expect($this->component->instance()->toggle()->getTargetUrl())->toBe($expected);
})->with([
    'mainnet' => ['production', 'https://test.arkscan.io'],
    'testnet' => ['development', 'https://live.arkscan.io'],
]);
