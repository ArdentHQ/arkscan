<?php

declare(strict_types=1);

use App\DTO\MemoryWallet;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\NetworkCache;
use App\ViewModels\BlockViewModel;
use Carbon\Carbon;
use function Spatie\Snapshots\assertMatchesSnapshot;

beforeEach(function () {
    $previousBlock = Block::factory()->create(['number' => 1]);

    $block = Block::factory()->create([
        'parent_hash' => $previousBlock->id,
        'number'      => 10000,
        'fee'         => 48 * 1e18,
        'reward'      => 2 * 1e18,
    ]);

    $this->subject = new BlockViewModel($block);
});

it('should get the url', function () {
    expect($this->subject->url())->toBeString();
    expect($this->subject->url())->toBe(route('block', $this->subject->hash()));
});

it('should get the timestamp', function () {
    expect($this->subject->timestamp())->toBeString();
    expect($this->subject->timestamp())->toBe('19 Oct 2020 04:54:16');
});

it('should get the dateTime', function () {
    expect($this->subject->dateTime())->toBeInstanceOf(Carbon::class);
    expect($this->subject->dateTime()->format('Y-m-d H:i:s'))->toBe('2020-10-19 04:54:16');
});

it('should get the height', function () {
    expect($this->subject->height())->toBeInt();
    expect($this->subject->height())->toBe(10000);
});

it('should get the fee', function () {
    expect($this->subject->fee())->toBeFloat();

    assertMatchesSnapshot($this->subject->fee());
});

it('should get the reward', function () {
    expect($this->subject->reward())->toBeFloat();

    assertMatchesSnapshot($this->subject->reward());
});

it('should get the total reward', function () {
    expect($this->subject->totalReward())->toBeFloat();

    assertMatchesSnapshot($this->subject->totalReward());
});

it('should get the fee as fiat', function () {
    expect($this->subject->feeFiat())->toBeString();
});

it('should get the reward as fiat', function () {
    expect($this->subject->rewardFiat())->toBeString();
});

it('should get the total reward as fiat', function () {
    expect($this->subject->totalRewardFiat())->toBeString();
});

it('should get the validator', function () {
    expect($this->subject->validator())->toBeInstanceOf(MemoryWallet::class);
});

it('should get the validator wallet name', function () {
    expect($this->subject->username())->toBeString();
    expect($this->subject->username())->toBe('Genesis');
});

it('should fail to get the validator wallet name', function () {
    $this->subject = new BlockViewModel(Block::factory()->create([
        'proposer' => Wallet::factory()->create([
            'attributes' => [],
        ])->address,
    ]));

    expect($this->subject->username())->toBeString();
    expect($this->subject->username())->toBe('Genesis');
});

it('should get the previous block url', function () {
    $previousBlock = Block::factory()->create(['number' => 1]);

    $subject = new BlockViewModel(Block::factory()->create([
        'parent_hash'    => $previousBlock->id,
        'number'         => 2,
    ]));

    expect($subject->previousBlockUrl())->toBeString();
});

it('should fail to get the previous block url', function () {
    $subject = new BlockViewModel(Block::factory()->create([
        'parent_hash'    => null,
        'number'         => 1,
    ]));

    expect($subject->previousBlockUrl())->toBeNull();
});

it('should get the next block url', function () {
    $previousBlock = Block::factory()->create(['number' => 2]);

    $subject = new BlockViewModel(Block::factory()->create([
        'parent_hash'    => $previousBlock->id,
        'number'         => 1,
    ]));

    expect($subject->nextBlockUrl())->toBeString();
});

it('should fail to get the next block url', function () {
    $previousBlock = Block::factory()->create(['number' => 1]);

    $subject = new BlockViewModel(Block::factory()->create([
        'parent_hash'    => $previousBlock->id,
        'number'         => 2,
    ]));

    expect($subject->nextBlockUrl())->toBeNull();
});

it('should get the confirmations', function () {
    (new NetworkCache())->setHeight(fn (): int => 500);

    expect($this->subject->confirmations())->toBe(10000 - 500);
});
