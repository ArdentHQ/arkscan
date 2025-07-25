<?php

declare(strict_types=1);

use App\Console\Commands\CacheValidatorsWithVoters;
use App\Facades\Network;
use App\Http\Livewire\Wallet\Tabs;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\WalletCache;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Livewire\Livewire;
use function Tests\fakeCryptoCompare;

beforeEach(function () {
    fakeCryptoCompare();
});

it('should render', function () {
    $wallet = Wallet::factory()->create();

    Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->set('view', 'voters')
        ->assertSet('isReady', false)
        ->assertSet('votersIsReady', false)
        ->assertSee('Showing 0 results')
        ->call('setVotersReady')
        ->assertSet('isReady', true)
        ->assertSet('votersIsReady', true)
        ->assertSee('Showing 0 results');
});

it('should list all voters for the given public key', function () {
    $wallet = Wallet::factory()
        ->activeValidator()
        ->create();

    $voters = Wallet::factory(10)->create([
        'attributes' => [
            'vote' => $wallet->address,
        ],
    ]);

    (new CacheValidatorsWithVoters())->handle(new WalletCache());

    (new NetworkCache())->setSupply(fn () => 10 * 1e18);

    $component = Livewire::test(Tabs::class, [new WalletViewModel($wallet)])
        ->set('view', 'voters')
        ->call('setVotersReady');

    foreach (ViewModelFactory::collection($voters) as $voter) {
        $component->assertSee($voter->address());
        $component->assertSeeInOrder([
            Network::currency(),
            number_format($voter->balance()),
        ]);
        $component->assertSee(NumberFormatter::percentage($voter->votePercentage()));
    }
});

it('should show no data if not ready', function () {
    $wallet = Wallet::factory()
        ->activeValidator()
        ->create();

    $voter = Wallet::factory()->create([
        'attributes' => [
            'vote' => $wallet->address,
        ],
    ]);

    Livewire::test(Tabs::class, [ViewModelFactory::make($wallet)])
        ->set('view', 'voters')
        ->assertDontSee($voter->address)
        ->assertSet('view', 'voters')
        ->call('setVotersReady')
        ->assertSet('votersIsReady', true)
        ->assertSee($voter->address);
});
