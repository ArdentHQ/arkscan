<?php

declare(strict_types=1);

use App\DTO\Slot;
use App\Models\Block;
use App\Models\Wallet;

use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use function Tests\configureExplorerDatabase;

it('should make an instance that has all properties', function (string $status) {
    configureExplorerDatabase();

    $wallet = Wallet::factory()->create();

    $subject = new Slot([
        'publicKey'    => $wallet->public_key,
        'order'        => 5,
        'wallet'       => new WalletViewModel($wallet),
        'forging_at'   => Carbon::now(),
        'last_block'   => [
            'publicKey' => $wallet->public_key,
            'height'    => Block::factory()->create()->height->toNumber(),
        ],
        'status' => $status,
        'time'   => 0,
    ], Block::whereBetween('height', [1, 5])->get(), 1);

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
})->with(['done', 'next', 'pending']);

it('should not be marked as missing if it never had a block', function () {
    configureExplorerDatabase();

    $wallet = Wallet::factory()->create();

    $subject = new Slot([
        'publicKey'    => $wallet->public_key,
        'last_block'   => [],
        'status'       => 'done',
    ], Block::whereBetween('height', [1, 5])->get(), 1);

    expect($subject->keepsMissing())->toBeFalse();
    expect($subject->missedCount())->toBe(0);
});

it('should show the correct missed blocks amount when spanning multiple rounds', function () {
    configureExplorerDatabase();

    $wallet = Wallet::factory()->create();

    $subject = new Slot([
        'publicKey'    => $wallet->public_key,
        'last_block'   => [
            'publicKey' => $wallet->public_key,
            'height'    => 1,
        ],
        'status'       => 'done',
    ], Block::whereBetween('height', [1, 5])->get(), 10);

    expect($subject->missedCount())->toBe(9);
});
