<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Http\Livewire\Validators\HeaderStats;
use App\Models\ForgingStats;
use App\Models\Wallet;
use App\Services\Cache\ValidatorCache;
use App\Services\Cache\WalletCache;
use Livewire\Livewire;
use function Tests\createRoundEntry;

beforeEach(function () {
    $this->wallets = Wallet::factory(Network::validatorCount())
        ->activeValidator()
        ->create();

    createRoundEntry(112168, (112168 - 1) * Network::validatorCount(), $this->wallets);
});

it('should render without errors', function () {
    $component = Livewire::test(HeaderStats::class);

    $component->assertHasNoErrors();
    $component->assertViewIs('livewire.validators.header-stats');
});

it('should not error if no validator data', function () {
    foreach ($this->wallets as $wallet) {
        expect((new WalletCache())->getValidator($wallet->address))->toBeNull();
    }

    Livewire::test(HeaderStats::class)
        ->assertViewHasAll([
            'voterCount'       => 0,
            'totalVoted'       => 0,
            'votesPercentage'  => 0,
            'missedBlocks'     => 0,
            'validatorsMissed' => 0,
        ]);
});

it('should show the correct number of votes', function () {
    (new ValidatorCache())->setTotalWalletsVoted(25);
    (new ValidatorCache())->setTotalBalanceVoted(200);

    Livewire::test(HeaderStats::class)
        ->assertViewHas('voterCount', 25)
        ->assertViewHas('totalVoted', 200);
});

it('should pluralize missed validator count', function ($count, $text) {
    ForgingStats::factory($count)->create([
        'forged' => false,
    ]);

    Livewire::test(HeaderStats::class)
        ->assertViewHas('validatorsMissed', $count)
        ->assertSeeInOrder([
            'Missed Blocks (30 Days)',
            $count,
            $count,
            $text,
            'View',
            'Voting (',
        ]);
})->with([
    0 => [0, 'Validators'],
    1 => [1, 'Validator'],
    2 => [2, 'Validators'],
    3 => [3, 'Validators'],
    4 => [4, 'Validators'],
]);
