<?php

declare(strict_types=1);

use App\Models\Block;
use App\ViewModels\BlockViewModel;

use function Tests\configureExplorerDatabase;

beforeEach(function () {
    configureExplorerDatabase();

    $previousBlock = Block::factory()->create(['height' => 1]);

    $this->subject = new BlockViewModel(Block::factory()->create([
        'previous_block' => $previousBlock->id,
        'height'         => 10000,
        'total_amount'   => 50 * 1e8,
        'total_fee'      => 48 * 1e8,
        'reward'         => 2 * 1e8,
    ]));
});

it('should get the url', function () {
    expect($this->subject->url())->toBeString();
    expect($this->subject->url())->toBe(route('block', $this->subject->id()));
});

it('should get the timestamp', function () {
    expect($this->subject->timestamp())->toBeString();
    expect($this->subject->timestamp())->toBe('19 Oct 2020 (04:54:16)');
});

it('should get the height', function () {
    expect($this->subject->height())->toBeString();
    expect($this->subject->height())->toBe('10,000');
});

it('should get the amount', function () {
    expect($this->subject->amount())->toBeString();
    expect($this->subject->amount())->toBe('ARK 50.00');
});

it('should get the fee', function () {
    expect($this->subject->fee())->toBeString();
    expect($this->subject->fee())->toBe('ARK 48.00');
});

it('should get the reward', function () {
    expect($this->subject->reward())->toBeString();
    expect($this->subject->reward())->toBe('ARK 2.00');
});
