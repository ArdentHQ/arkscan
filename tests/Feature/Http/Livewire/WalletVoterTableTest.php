<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\WalletVoterTable;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;
use function Tests\fakeCryptoCompare;

beforeEach(function () {
    fakeCryptoCompare();

    $this->subject = Wallet::factory()->create();
});

it('should list all blocks for the given public key', function () {
    $voters = Wallet::factory(10)->create([
        'attributes' => [
            'vote' => $this->subject->public_key,
        ],
    ]);

    (new NetworkCache())->setSupply(fn () => '1000000000');

    $component = Livewire::test(WalletVoterTable::class, [$this->subject->public_key, 'username']);

    foreach (ViewModelFactory::collection($voters) as $voter) {
        $component->assertSee($voter->address());
        $component->assertSeeInOrder([
            Network::currency(),
            number_format($voter->balance()),
        ]);
        $component->assertSee(NumberFormatter::percentage($voter->votePercentage()));
    }
});
