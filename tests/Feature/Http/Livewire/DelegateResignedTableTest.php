<?php

declare(strict_types=1);

use App\Http\Livewire\DelegateResignedTable;
use Livewire\Livewire;

it('should render the component without data', function (): void {
    Livewire::test(DelegateResignedTable::class)
        ->assertSet('load', false)
        ->emit('tabFiltered', 'active')
        ->assertSet('load', false);
});

it('should render the component with data', function (): void {
    Livewire::test(DelegateResignedTable::class)
        ->assertSet('load', false)
        ->emit('tabFiltered', 'resigned')
        ->assertSet('load', true);
});
