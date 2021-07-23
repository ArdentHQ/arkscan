<?php

declare(strict_types=1);

use App\Http\Livewire\DelegateTabs;
use Livewire\Livewire;

// @TODO: make assertions about data visibility
it('should render without errors', function () {
    $component = Livewire::test(DelegateTabs::class);
});
