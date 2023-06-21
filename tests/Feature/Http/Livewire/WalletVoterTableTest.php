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
            'vote' => $wallet->public_key,
        ],
    ]);

    (new NetworkCache())->setSupply(fn () => '1000000000');

    $component = Livewire::test(WalletVoterTable::class, [new WalletViewModel($wallet)]);

    foreach (ViewModelFactory::collection($voters) as $voter) {
        $component->assertSee($voter->address());
        $component->assertSeeInOrder([
            Network::currency(),
            $voter->balance(),
        ]);
        $component->assertSee(NumberFormatter::percentage($voter->votePercentage()));
    }
});

it('should handle cold wallets without a public key', function () {
    $wallet = Wallet::factory()->create([
        'public_key' => null,
    ]);

    Livewire::test(WalletVoterTable::class, [new WalletViewModel($wallet)])
        ->assertSee(trans('tables.wallets.no_results'));
});
