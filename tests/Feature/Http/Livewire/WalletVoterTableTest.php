<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\WalletVoterTable;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Livewire\Livewire;
use function Tests\fakeCryptoCompare;

beforeEach(function () {
    fakeCryptoCompare();
});

it('should list all voters for the given public key', function () {
    $wallet = Wallet::factory()->create();

    $voters = Wallet::factory(10)->create([
        'attributes' => [
            'vote' => $wallet->address,
        ],
    ]);

    (new NetworkCache())->setSupply(fn () => 10 * 1e18);

    $component = Livewire::test(WalletVoterTable::class, [new WalletViewModel($wallet)])
        ->call('setIsReady');

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
    $wallet = Wallet::factory()->create();

    $voter = Wallet::factory()->create([
        'attributes' => [
            'vote' => $wallet->address,
        ],
    ]);

    Livewire::test(WalletVoterTable::class, [ViewModelFactory::make($wallet)])
        ->assertDontSee($voter->address)
        ->call('setIsReady')
        ->assertSee($voter->address);
});
