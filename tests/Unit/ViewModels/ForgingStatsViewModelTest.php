<?php

declare(strict_types=1);

use App\Models\ForgingStats;
use App\Models\Wallet;
use App\ViewModels\ForgingStatsViewModel;
use Carbon\Carbon;

beforeEach(function () {
    ForgingStats::truncate();

    $this->subject = new ForgingStatsViewModel(ForgingStats::factory()->create([
        'missed_height' => 54321,
        'timestamp'     => 1490103134,
    ]));
});

it('should get the timestamp', function () {
    expect($this->subject->timestamp())->toBeString();
    expect($this->subject->timestamp())->toBe('21 Mar 2017 13:32:14');
});

it('should get the dateTime', function () {
    expect($this->subject->dateTime())->toBeInstanceOf(Carbon::class);
    expect($this->subject->dateTime()->format('Y-m-d H:i:s'))->toBe('2017-03-21 13:32:14');
});

it('should get the height', function () {
    expect($this->subject->height())->toBeInt();
    expect($this->subject->height())->toBe(54321);
});

it('should get the validator address', function () {
    $wallet = Wallet::factory()->activeValidator()->create();

    $this->subject = new ForgingStatsViewModel(ForgingStats::factory()->create([
        'public_key' => $wallet->public_key,
    ]));

    expect($this->subject->validator()->address())->toBe($wallet->address);
});

it('should handle no validator', function () {
    $this->subject = new ForgingStatsViewModel(ForgingStats::factory()->create([
        'public_key' => 'key-to-missing-validator',
    ]));

    expect($this->subject->validator())->toBeNull();
});

it('should get the validator username', function () {
    $wallet = Wallet::factory()
        ->activeValidator()
        ->create([
            'attributes' => [
                'username' => 'joe.blogs',
            ],
        ]);

    $this->subject = new ForgingStatsViewModel(ForgingStats::factory()->create([
        'public_key' => $wallet->public_key,
    ]));

    expect($this->subject->username())->toBeString();
    expect($this->subject->username())->toBe('joe.blogs');
});

it('should handle no validator username', function () {
    $wallet = Wallet::factory()->activeValidator()->create(['attributes' => []]);

    $this->subject = new ForgingStatsViewModel(ForgingStats::factory()->create([
        'public_key' => $wallet->public_key,
    ]));

    expect($this->subject->username())->toBeNull();
});
