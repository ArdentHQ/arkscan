<?php

declare(strict_types=1);

use App\Http\Livewire\DelegateActiveTable;
use Livewire\Livewire;

it('should render the component without data', function (): void {
    Livewire::test(DelegateActiveTable::class)
        ->assertSet('load', false)
        ->emit('tabFiltered', 'standby')
        ->assertSet('load', false);
});

it('should render the component with data', function (): void {
    Livewire::test(DelegateActiveTable::class)
        ->assertSet('load', false)
        ->emit('tabFiltered', 'active')
        ->assertSet('load', true);
});
