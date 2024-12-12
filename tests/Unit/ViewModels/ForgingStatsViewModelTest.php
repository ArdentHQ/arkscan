<?php

declare(strict_types=1);

use App\Contracts\Network as Contract;
use App\Models\ForgingStats;
use App\Models\Wallet;
use App\Services\Blockchain\NetworkFactory;
use App\ViewModels\ForgingStatsViewModel;
use Carbon\Carbon;
use function Tests\fakeKnownWallets;

beforeEach(function () {
    $this->app->singleton(Contract::class, fn () => NetworkFactory::make('production'));

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
        'address' => $wallet->address,
    ]));

    expect($this->subject->validator()->address())->toBe($wallet->address);
});

it('should handle no validator', function () {
    $this->subject = new ForgingStatsViewModel(ForgingStats::factory()->create([
        'address' => 'address-to-missing-validator',
    ]));

    expect($this->subject->validator())->toBeNull();
});

it('should get the validator username', function () {
    fakeKnownWallets();

    $wallet = Wallet::factory()
        ->activeValidator()
        ->create(['address' => '0xEd0C906b8fcCDe71A19322DFfe929c6e04460cFF']);

    $this->subject = new ForgingStatsViewModel(ForgingStats::factory()->create([
        'address' => $wallet->address,
    ]));

    expect($this->subject->walletName())->toBeString();
    expect($this->subject->walletName())->toBe('Binance');
});

it('should handle no validator username', function () {
    $wallet = Wallet::factory()->activeValidator()->create(['attributes' => []]);

    $this->subject = new ForgingStatsViewModel(ForgingStats::factory()->create([
        'address' => $wallet->address,
    ]));

    expect($this->subject->walletName())->toBeNull();
});
