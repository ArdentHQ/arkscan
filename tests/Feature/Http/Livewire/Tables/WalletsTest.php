<?php

declare(strict_types=1);

use App\Http\Livewire\Tables\Wallets;
use App\Models\Wallet;
use App\ViewModels\ViewModelFactory;

use Livewire\Livewire;
use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should list the first page of records', function () {
    Wallet::factory(30)->create();

    $component = Livewire::test(Wallets::class, [
        'wallets' => Wallet::wealthy(),
    ]);

    foreach (ViewModelFactory::paginate(Wallet::wealthy()->paginate())->items() as $wallet) {
        $component->assertSee($wallet->address());
        $component->assertSee($wallet->balance());
    }
});
