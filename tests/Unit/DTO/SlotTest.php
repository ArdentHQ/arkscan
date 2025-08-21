<?php

declare(strict_types=1);

use App\DTO\Slot;
use App\Models\Block;
use App\Models\ForgingStats;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Cache\WalletCache;
use App\Services\Timestamp;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;

it('should make an instance that has all properties', function (string $status) {
    $wallet = Wallet::factory()->create();

    $lastBlockHeight = 7521;

    Block::factory()->create(['number' => $lastBlockHeight]);
    Round::factory()->create([
        'round_height' => 7520,
    ]);

    $roundBlockCount = Block::whereBetween('number', [1, 5])
        ->get()
        ->groupBy('proposer')
        ->map(function ($blocks) {
            return count($blocks);
        });

    $subject = new Slot(
        address: $wallet->address,
        order: 5,
        wallet: new WalletViewModel($wallet),
        forgingAt: Carbon::now(),
        lastBlock: [
            'address'   => $wallet->address,
            'height'    => $lastBlockHeight,
        ],
        status: $status,
        roundBlockCount: $roundBlockCount,
        roundNumber: 1,
        secondsUntilForge: 0,
    );

    expect($subject->address())->toBeString();
    expect($subject->order())->toBeInt();
    expect($subject->wallet())->toBeInstanceOf(WalletViewModel::class);
    expect($subject->forgingAt())->toBeInstanceOf(Carbon::class);
    expect($subject->lastBlock())->toBeArray();
    expect($subject->roundNumber())->toBeInt();
    expect($subject->hasForged())->toBeBool();
    expect($subject->justMissed())->toBeBool();
    expect($subject->keepsMissing())->toBeBool();
    expect($subject->missedCount())->toBeInt();
    expect($subject->isDone())->toBeBool();
    expect($subject->isNext())->toBeBool();
    expect($subject->isPending())->toBeBool();
    expect($subject->status())->toBeString();
    expect($subject->secondsUntilForge())->toBeInt();
    expect($subject->currentRoundBlocks())->toBeInt();
})->with([
    'done',
    'next',
    'pending',
]);

it('should not be marked as missing if it never had a block', function () {
    $wallet = Wallet::factory()->create();

    $roundBlockCount = Block::whereBetween('number', [1, 5])
        ->get()
        ->groupBy('proposer')
        ->map(function ($blocks) {
            return count($blocks);
        });

    $subject = new Slot(
        address: $wallet->address,
        order: 1,
        wallet: ViewModelFactory::make($wallet),
        forgingAt: Timestamp::fromGenesis(1),
        lastBlock: [],
        status: 'done',
        roundBlockCount: $roundBlockCount,
        roundNumber: 1,
        secondsUntilForge: 0,
    );

    expect($subject->keepsMissing())->toBeFalse();
    $this->assertDatabaseMissing('forging_stats', [
        'address' => $wallet->address,
    ]);
    expect($subject->missedCount())->toBe(0);
    expect($subject->currentRoundBlocks())->toBe(0);
});

it('should show the correct missed blocks amount when spanning multiple rounds', function () {
    $wallet = Wallet::factory()->create();

    $roundBlockCount = Block::whereBetween('number', [1, 5])
        ->get()
        ->groupBy('proposer')
        ->map(function ($blocks) {
            return count($blocks);
        });

    $subject = new Slot(
        address: $wallet->address,
        order: 1,
        wallet: ViewModelFactory::make($wallet),
        forgingAt: Timestamp::fromGenesis(1),
        lastBlock: [
            'address'   => $wallet->address,
            'height'    => 1,
        ],
        status: 'done',
        roundBlockCount: $roundBlockCount,
        roundNumber: 10,
        secondsUntilForge: 0,
    );

    expect(ForgingStats::where('address', $wallet->address)->exists())->toBeFalse();

    ForgingStats::create([
        'timestamp'  => 1,
        'address'    => $wallet->address,
        'forged'     => true,
    ]);

    ForgingStats::create([
        'timestamp'  => 2,
        'address'    => $wallet->address,
        'forged'     => false,
    ]);

    ForgingStats::create([
        'timestamp'  => 3,
        'address'    => $wallet->address,
        'forged'     => false,
    ]);

    expect(ForgingStats::where('address', $wallet->address)->exists())->toBeTrue();

    $missed = ForgingStats::where('forged', false)->where('address', $wallet->address)->count();

    (new WalletCache())->setMissedBlocks(
        $wallet->address,
        $missed
    );

    expect($subject->missedCount())->toBe(2);
    expect($subject->currentRoundBlocks())->toBe(0);
});

it('should convert to array', function () {
    $wallet = Wallet::factory()->create([
        'balance' => BigNumber::new(1000),
    ])->fresh();

    $roundBlockCount = Block::whereBetween('number', [1, 5])
        ->get()
        ->groupBy('proposer')
        ->map(function ($blocks) {
            return count($blocks);
        });

    $subject = new Slot(
        address: $wallet->address,
        order: 1,
        wallet: ViewModelFactory::make($wallet),
        forgingAt: Timestamp::fromGenesis(1),
        lastBlock: [
            'address'   => $wallet->address,
            'height'    => 1,
        ],
        status: 'done',
        roundBlockCount: $roundBlockCount,
        roundNumber: 10,
        secondsUntilForge: 24,
    );

    expect($subject->toArray())->toEqual([
        'address'           => $wallet->address,
        'order'             => 1,
        'wallet'            => [
            ...$wallet->toArray(),

            'isPending'   => false,
            'hasForged'   => false,
            'justMissed'  => true,
            'missedCount' => 0,
        ],
        'forgingAt'         => Timestamp::fromGenesis(1)->toIso8601String(),
        'lastBlock'         => [
            'address'   => $wallet->address,
            'height'    => 1,
        ],
        'status'            => 'done',
        'roundBlockCount'   => $roundBlockCount->toArray(),
        'roundNumber'       => 10,
        'secondsUntilForge' => 24,
    ]);
});
