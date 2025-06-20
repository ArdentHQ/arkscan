<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\Validators\Tabs;
use Livewire\Livewire;

it('should render', function () {
    Livewire::test(Tabs::class)
        ->assertSee('Validators');
});

it('should change view with event', function () {
    Livewire::test(Tabs::class)
        ->assertSet('view', 'validators')
        ->dispatch('showValidatorsView', 'missed-blocks')
        ->assertSet('view', 'missed-blocks')
        ->dispatch('showValidatorsView', 'validators')
        ->assertSet('view', 'validators');
});

it('should sync input for non-existent property value', function () {
    Livewire::test(Tabs::class)
        ->set('view', 'validators')
        ->call('syncInput', 'testProperty', true)
        ->assertSet('testProperty', true);
});
