<?php

declare(strict_types=1);

use App\Models\Block;
use App\ViewModels\BlockViewModel;
use Carbon\Carbon;

beforeEach(function () {
    $block = Block::factory()->create([
        'number'    => 10000,
        'fee'       => 48 * 1e18,
        'reward'    => 2 * 1e18,
    ]);

    $this->subject = new BlockViewModel($block);
});

it('should get the hash', function () {
    expect($this->subject->hash())->toBeString();
    expect($this->subject->hash())->toBe($this->subject->hash());
});

it('should get the dateTime', function () {
    expect($this->subject->dateTime())->toBeInstanceOf(Carbon::class);
    expect($this->subject->dateTime()->format('Y-m-d H:i:s'))->toBe('2020-10-19 04:54:16');
});
