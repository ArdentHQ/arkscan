<?php

declare(strict_types=1);

use App\DTO\Slot;
use App\Models\Block;
use App\Models\ForgingStats;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Cache\WalletCache;
use App\Services\Timestamp;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;

beforeEach(function () {
    ForgingStats::truncate();
});

it('should make an instance that has all properties', function (string $status) {
    $wallet = Wallet::factory()->create();

    $lastBlockHeight = 7521;

    Block::factory()->create(['height' => $lastBlockHeight]);
    Round::factory()->create([
        'round_height' => 7520,
    ]);

    $roundBlockCount = Block::whereBetween('height', [1, 5])
        ->get()
        ->groupBy('generator_address')
        ->map(function ($blocks) {
            return count($blocks);
        });

    $subject = new Slot(
        publicKey: $wallet->public_key,
        order: 5,
        wallet: new WalletViewModel($wallet),
        forgingAt: Carbon::now(),
        lastBlock: [
            'publicKey' => $wallet->public_key,
            'height'    => $lastBlockHeight,
        ],
        status: $status,
        roundBlockCount: $roundBlockCount,
        roundNumber: 1,
        secondsUntilForge: 0,
    );

    expect($subject->publicKey())->toBeString();
    expect($subject->order())->toBeInt();
    expect($subject->wallet())->toBeInstanceOf(WalletViewModel::class);
    expect($subject->forgingAt())->toBeInstanceOf(Carbon::class);
    expect($subject->lastBlock())->toBeArray();
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

    $roundBlockCount = Block::whereBetween('height', [1, 5])
        ->get()
        ->groupBy('generator_address')
        ->map(function ($blocks) {
            return count($blocks);
        });

    $subject = new Slot(
        publicKey: $wallet->public_key,
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
        'public_key' => $wallet->public_key,
    ]);
    expect($subject->missedCount())->toBe(0);
    expect($subject->currentRoundBlocks())->toBe(0);
});

it('should show the correct missed blocks amount when spanning multiple rounds', function () {
    $wallet = Wallet::factory()->create();

    $roundBlockCount = Block::whereBetween('height', [1, 5])
        ->get()
        ->groupBy('generator_address')
        ->map(function ($blocks) {
            return count($blocks);
        });

    $subject = new Slot(
        publicKey: $wallet->public_key,
        order: 1,
        wallet: ViewModelFactory::make($wallet),
        forgingAt: Timestamp::fromGenesis(1),
        lastBlock: [
            'publicKey' => $wallet->public_key,
            'height'    => 1,
        ],
        status: 'done',
        roundBlockCount: $roundBlockCount,
        roundNumber: 10,
        secondsUntilForge: 0,
    );

    expect(ForgingStats::where('public_key', $wallet->public_key)->exists())->toBeFalse();

    ForgingStats::create([
        'timestamp'  => 1,
        'public_key' => $wallet->public_key,
        'forged'     => true,
    ]);

    ForgingStats::create([
        'timestamp'  => 2,
        'public_key' => $wallet->public_key,
        'forged'     => false,
    ]);

    ForgingStats::create([
        'timestamp'  => 3,
        'public_key' => $wallet->public_key,
        'forged'     => false,
    ]);

    expect(ForgingStats::where('public_key', $wallet->public_key)->exists())->toBeTrue();

    $missed = ForgingStats::where('forged', false)->where('public_key', $wallet->public_key)->count();

    (new WalletCache())->setMissedBlocks(
        $wallet->public_key,
        $missed
    );

    expect($subject->missedCount())->toBe(2);
    expect($subject->currentRoundBlocks())->toBe(0);
});
