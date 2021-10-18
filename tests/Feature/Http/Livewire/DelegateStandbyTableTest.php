<?php

declare(strict_types=1);

use App\Http\Livewire\DelegateStandbyTable;
use App\Models\Block;
use App\Models\Round;
use App\Models\Scopes\StandbyDelegateScope;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;

it('should render the component without data', function (): void {
    Livewire::test(DelegateStandbyTable::class)
        ->assertSet('load', false)
        ->emit('tabFiltered', 'active')
        ->assertSet('load', false);
});

it('should render the component with data', function (): void {
    Livewire::test(DelegateStandbyTable::class)
        ->assertSet('load', false)
        ->emit('tabFiltered', 'standby')
        ->assertSet('load', true);
});

it('should list the first page of records', function (): void {
    $wallets = Wallet::factory()->count(102)->standbyDelegate()->create();

    $wallets->each(function ($wallet) {
        Round::factory()->create([
            'round'      => '1',
            'public_key' => $wallet->public_key,
        ]);

        for ($i = 0; $i < 3; $i++) {
            Block::factory()->create([
                'height'               => $i,
                'generator_public_key' => $wallet->public_key,
            ]);
        }

        (new WalletCache())->setDelegate($wallet->public_key, $wallet);
    });

    $component = Livewire::test(DelegateStandbyTable::class);

    $component->emit('tabFiltered', 'standby');

    foreach (ViewModelFactory::paginate(Wallet::withScope(StandbyDelegateScope::class)->paginate())->items() as $delegate) {
        $component->assertSee($delegate->rank());
        $component->assertSee($delegate->username());
        $component->assertSee(NumberFormatter::number($delegate->votes()));
    }
});
