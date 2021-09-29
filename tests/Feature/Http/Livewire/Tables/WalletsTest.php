<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\Tables\Wallets;
use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;

it('should list the first page of records', function () {
    (new NetworkCache())->setSupply(fn () => strval(10e8));

    Wallet::factory(30)->create();

    $component = Livewire::test(Wallets::class, [
        'wallets' => Wallet::withScope(OrderByBalanceScope::class),
    ]);

    foreach (ViewModelFactory::paginate(Wallet::withScope(OrderByBalanceScope::class)->paginate())->items() as $wallet) {
        $component->assertSee($wallet->address());
        $component->assertSee(NumberFormatter::currency($wallet->balance(), Network::currency()));
    }
});
