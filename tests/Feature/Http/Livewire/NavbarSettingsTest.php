<?php

declare(strict_types=1);

use App\Http\Livewire\NavbarSettings;
use Livewire\Livewire;

it('should update the currency', function () {
    Livewire::test(NavbarSettings::class)
        ->assertSet('state.currency', 'usd')
        ->set('state.currency', 'chf')
        ->assertSet('state.currency', 'chf');
});
