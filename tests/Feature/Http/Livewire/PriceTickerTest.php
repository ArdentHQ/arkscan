<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\PriceTicker;
use App\Services\Settings;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

it('should render with the source currency, target currency and exchange rate', function () {
    Http::fakeSequence()
        ->push(['USD' => 0.2907]);

    Livewire::test(PriceTicker::class)
        ->assertSee(Network::currency())
        ->assertSee(Settings::currency())
        ->assertSee(0.2907);
});
