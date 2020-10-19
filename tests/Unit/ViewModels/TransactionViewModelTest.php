<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Transaction;

use App\ViewModels\TransactionViewModel;
use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    $block = Block::factory()->create(['height' => 1]);
    Block::factory()->create(['height' => 5000000]);

    $this->subject = new TransactionViewModel(Transaction::factory()->create([
        'block_id' => $block->id,
        'fee'      => 1 * 1e8,
        'amount'   => 2 * 1e8,
    ]));
});

it('should get the timestamp', function () {
    expect($this->subject->timestamp())->toBeString();
    expect($this->subject->timestamp())->toBe('19 Oct 2020 (04:54:16)');
});

it('should get the fee', function () {
    expect($this->subject->fee())->toBeString();
    expect($this->subject->fee())->toBe('ARK 1.00');
});

it('should get the amount', function () {
    expect($this->subject->amount())->toBeString();
    expect($this->subject->amount())->toBe('ARK 2.00');
});

it('should get the confirmations', function () {
    expect($this->subject->confirmations())->toBeString();
    expect($this->subject->confirmations())->toBe('4,999,999');
});
