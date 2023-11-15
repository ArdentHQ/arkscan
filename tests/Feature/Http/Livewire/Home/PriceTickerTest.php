<?php

declare(strict_types=1);

use App\Http\Livewire\Home\PriceTicker;
use App\Services\Cache\NetworkStatusBlockCache;
use Livewire\Livewire;

it('should render price', function () {
    (new NetworkStatusBlockCache())->setPrice('DARK', 'USD', 0.2907);

    Livewire::test(PriceTicker::class)
        ->assertSee('$0.29');
});
