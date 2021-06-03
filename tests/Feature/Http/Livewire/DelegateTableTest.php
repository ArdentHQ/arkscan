<?php

declare(strict_types=1);

use App\Http\Livewire\DelegateTable;
use App\Models\Block;
use App\Models\Round;
use App\Models\Scopes\StandbyDelegateScope;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;

use function Tests\configureExplorerDatabase;

beforeEach(fn () => configureExplorerDatabase());

it('should render with all delegates', function () {
    $component = Livewire::test(DelegateTable::class);

    $component->emit('filterByDelegateStatus', 'active');

    expect($component->instance()->state['status'])->toBe('active');
});

it('should render with standby delegates', function () {
    $component = Livewire::test(DelegateTable::class);

    $component->emit('filterByDelegateStatus', 'standby');

    expect($component->instance()->state['status'])->toBe('standby');
});

it('should render with resigned delegates', function () {
    $component = Livewire::test(DelegateTable::class);

    $component->emit('filterByDelegateStatus', 'resigned');

    expect($component->instance()->state['status'])->toBe('resigned');
});

it('should list the first page of records', function () {
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

    $component = Livewire::test(DelegateTable::class);

    $component->emit('filterByDelegateStatus', 'standby');

    foreach (ViewModelFactory::paginate(Wallet::withScope(StandbyDelegateScope::class)->paginate())->items() as $delegate) {
        $component->assertSee($delegate->rank());
        $component->assertSee($delegate->username());
        $component->assertSee(NumberFormatter::number($delegate->votes()));
    }
});
