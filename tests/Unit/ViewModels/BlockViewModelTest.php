<?php

declare(strict_types=1);

use App\DTO\MemoryWallet;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Cache\CryptoDataCache;
use App\Services\Cache\NetworkCache;
use App\Services\NumberFormatter;
use App\ViewModels\BlockViewModel;
use App\ViewModels\TransactionViewModel;
use Carbon\Carbon;
use function Spatie\Snapshots\assertMatchesSnapshot;

beforeEach(function () {
    $previousBlock = Block::factory()->create(['height' => 1]);

    $this->subject = new BlockViewModel(Block::factory()->create([
        'previous_block' => $previousBlock->id,
        'height'         => 10000,
        'total_amount'   => '5000000000',
        'total_fee'      => '4800000000',
        'reward'         => '200000000',
    ]));
});

it('should get the url', function () {
    expect($this->subject->url())->toBeString();
    expect($this->subject->url())->toBe(route('block', $this->subject->id()));
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

it('should get the amount for different transaction types', function () {
    $transactions = Transaction::factory(10)
        ->transfer()
        ->create([
            'block_id' => $this->subject->id(),
        ])->concat(
            Transaction::factory(10)
                ->vote()
                ->create([
                    'block_id' => $this->subject->id(),
                ])
        )->concat(
            Transaction::factory(10)
                ->multiPayment()
                ->create([
                    'block_id' => $this->subject->id(),
                ])
        );

    $amount = 0;
    foreach ($transactions as $transaction) {
        $amount += (new TransactionViewModel($transaction))->amount();
    }

    expect($this->subject->amount())->toBeFloat();
    expect($this->subject->amount())->toEqual($amount);
});

it('should get the amount as fiat', function () {
    $exchangeRate = 0.1234567;
    app(CryptoDataCache::class)->setPrices('USD.week', collect([
        '2020-10-19' => $exchangeRate,
    ]));

    $transactions = Transaction::factory(10)
        ->transfer()
        ->create([
            'block_id'  => $this->subject->id(),
            'timestamp' => Carbon::parse('2020-10-19 00:00:00')->timestamp,
        ])->concat(
            Transaction::factory(10)
                ->vote()
                ->create([
                    'block_id'  => $this->subject->id(),
                    'timestamp' => Carbon::parse('2020-10-19 00:00:00')->timestamp,
                ])
        )->concat(
            Transaction::factory(10)
                ->multiPayment()
                ->create([
                    'block_id'  => $this->subject->id(),
                    'timestamp' => Carbon::parse('2020-10-19 00:00:00')->timestamp,
                ])
        );

    $amount = 0;
    foreach ($transactions as $transaction) {
        $amount += (new TransactionViewModel($transaction))->amount();
    }

    expect($this->subject->amountFiat())->toBeString();
    expect($this->subject->amountFiat())->toEqual(NumberFormatter::currency($amount * $exchangeRate, 'USD'));
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

it('should get the delegate', function () {
    expect($this->subject->delegate())->toBeInstanceOf(MemoryWallet::class);
});

it('should get the delegate username', function () {
    expect($this->subject->username())->toBeString();
    expect($this->subject->username())->toBe('Genesis');
});

it('should fail to get the delegate username', function () {
    $this->subject = new BlockViewModel(Block::factory()->create([
        'generator_public_key' => Wallet::factory()->create([
            'attributes' => [],
        ])->public_key,
    ]));

    expect($this->subject->username())->toBeString();
    expect($this->subject->username())->toBe('Genesis');
});

it('should get the previous block url', function () {
    $previousBlock = Block::factory()->create(['height' => 1]);

    $subject = new BlockViewModel(Block::factory()->create([
        'previous_block' => $previousBlock->id,
        'height'         => 2,
    ]));

    expect($subject->previousBlockUrl())->toBeString();
});

it('should fail to get the previous block url', function () {
    $subject = new BlockViewModel(Block::factory()->create([
        'previous_block' => null,
        'height'         => 1,
    ]));

    expect($subject->previousBlockUrl())->toBeNull();
});

it('should get the next block url', function () {
    $previousBlock = Block::factory()->create(['height' => 2]);

    $subject = new BlockViewModel(Block::factory()->create([
        'previous_block' => $previousBlock->id,
        'height'         => 1,
    ]));

    expect($subject->nextBlockUrl())->toBeString();
});

it('should fail to get the next block url', function () {
    $previousBlock = Block::factory()->create(['height' => 1]);

    $subject = new BlockViewModel(Block::factory()->create([
        'previous_block' => $previousBlock->id,
        'height'         => 2,
    ]));

    expect($subject->nextBlockUrl())->toBeNull();
});

it('should get the confirmations', function () {
    (new NetworkCache())->setHeight(fn (): int => 500);

    expect($this->subject->confirmations())->toBe(10000 - 500);
});
