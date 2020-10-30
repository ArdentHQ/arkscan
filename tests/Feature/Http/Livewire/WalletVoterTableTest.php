<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\WalletVoterTable;
use App\Models\Wallet;
use App\Services\NumberFormatter;
use App\ViewModels\ViewModelFactory;
use Livewire\Livewire;
use function Tests\configureExplorerDatabase;
use function Tests\fakeCryptoCompare;

beforeEach(function () {
    fakeCryptoCompare();

    configureExplorerDatabase();

    $this->subject = Wallet::factory()->create();
});

it('should list all blocks for the given public key', function () {
    $voters = Wallet::factory(10)->create([
        'attributes' => [
            'vote' => $this->subject->public_key,
        ],
    ]);

    $component = Livewire::test(WalletVoterTable::class, [$this->subject->public_key]);

    foreach (ViewModelFactory::collection($voters) as $voter) {
        $component->assertSee($voter->address());
        $component->assertSee($voter->balance());
        $component->assertSee(NumberFormatter::currency($voter->balance(), Network::currency()));
        $component->assertSee(NumberFormatter::percentage($voter->balancePercentage()));
    }
});
