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

    $this->subject = Wallet::factory()->create();
});

it('should list all voters for the given public key', function () {
    $voters = Wallet::factory(10)->create([
        'attributes' => [
            'vote' => $this->subject->public_key,
        ],
    ]);

    (new NetworkCache())->setSupply(fn () => '1000000000');

    $component = Livewire::test(WalletVoterTable::class, [new WalletViewModel($this->subject)]);

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
    Livewire::test(WalletVoterTable::class, [new WalletViewModel($this->subject)])
        ->assertSee(trans('tables.wallets.no_results'));
});
