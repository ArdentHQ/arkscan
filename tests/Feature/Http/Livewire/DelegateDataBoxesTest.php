<?php

declare(strict_types=1);

use App\Http\Livewire\DelegateDataBoxes;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use Livewire\Livewire;

beforeEach(function () {
    $this->wallets = Wallet::factory(51)
        ->activeDelegate()
        ->create()
        ->each(function ($wallet) {
            Round::factory()->create([
                'round'      => '112168',
                'public_key' => $wallet->public_key,
            ]);
        });
});

it('should render without errors', function () {
    $component = Livewire::test(DelegateDataBoxes::class);

    $component->assertHasNoErrors();
    $component->assertViewIs('livewire.delegate-data-boxes');
});

it('should not error if no delegate data', function () {
    foreach ($this->wallets as $wallet) {
        expect((new WalletCache())->getDelegate($wallet->public_key))->toBeNull();
    }

    Livewire::test(DelegateDataBoxes::class)
        ->assertViewHasAll([
            'voterCount'      => 0,
            'totalVoted'      => 0,
            'currentSupply'   => 0,
            'missedBlocks'    => 0,
            'delegatesMissed' => 0,
        ]);
});

it('should show the correct number of votes', function () {
    Wallet::factory(20)
        ->create([
            'balance'    => '1000000000',
            'attributes' => [
                'vote' => 'publickey',
            ],
        ]);

    Wallet::factory(5)
        ->create([
            'balance'    => '0',
            'attributes' => [
                'vote' => 'publickey',
            ],
        ]);

    Wallet::factory(5)
        ->create([
            'balance'    => '1000000000',
            'attributes' => [],
        ]);

    Livewire::test(DelegateDataBoxes::class)
        ->assertViewHas('voterCount', 25)
        ->assertViewHas('totalVoted', 200);
});
