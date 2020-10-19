<?php

declare(strict_types=1);

use App\Http\Livewire\SearchModule;
use Livewire\Livewire;

use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should search by type', function () {
    Livewire::test(SearchModule::class)
        ->call('performSearch');
});

it('should search by amount range', function () {
    Livewire::test(SearchModule::class)
        ->call('performSearch');
});

it('should search by fee range', function () {
    Livewire::test(SearchModule::class)
        ->call('performSearch');
});

it('should search by date range', function () {
    Livewire::test(SearchModule::class)
        ->call('performSearch');
});

it('should search by vendor field', function () {
    Livewire::test(SearchModule::class)
        ->call('performSearch');
});
