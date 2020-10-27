<?php

declare(strict_types=1);

use App\Http\Livewire\MonitorDelegateTable;
use Livewire\Livewire;

use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

// @TODO: make assertions about data visibility
it('should render with all delegates', function () {
    $component = Livewire::test(MonitorDelegateTable::class);
    $component->emit('filterByDelegateStatus', 'all');
});

// @TODO: make assertions about data visibility
it('should render with standby delegates', function () {
    $component = Livewire::test(MonitorDelegateTable::class);
    $component->emit('filterByDelegateStatus', 'standby');
});

// @TODO: make assertions about data visibility
it('should render with resigned delegates', function () {
    $component = Livewire::test(MonitorDelegateTable::class);
    $component->emit('filterByDelegateStatus', 'resigned');
});
