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

    $this->subject = Wallet::factory()
        ->activeValidator()
        ->create();
});

it('should render', function () {
    $this->subject = Wallet::factory()->create();

    Livewire::test(Tabs::class, [new WalletViewModel($this->subject)])
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
    $voters = Wallet::factory(10)->create([
        'attributes' => [
            'vote' => $this->subject->address,
        ],
    ]);

    (new CacheValidatorsWithVoters())->handle(new WalletCache());

    (new NetworkCache())->setSupply(fn () => 10 * 1e18);

    $component = Livewire::test(Tabs::class, [new WalletViewModel($this->subject)])
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
    $voter = Wallet::factory()->create([
        'attributes' => [
            'vote' => $this->subject->address,
        ],
    ]);

    Livewire::test(Tabs::class, [ViewModelFactory::make($this->subject)])
        ->set('view', 'voters')
        ->assertDontSee($voter->address)
        ->assertSet('view', 'voters')
        ->call('setVotersReady')
        ->assertSet('votersIsReady', true)
        ->assertSee($voter->address);
});

it('should have querystring data', function () {
    $instance = Livewire::test(Tabs::class, [ViewModelFactory::make($this->subject)])
        ->instance();

    expect($instance->getListenersVotersTab())->toBe(['reloadVoters' => '$refresh']);
});
