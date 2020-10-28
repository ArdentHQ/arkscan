<?php

declare(strict_types=1);

use App\DTO\Slot;
use App\Models\Wallet;
use App\ViewModels\WalletViewModel;

use Carbon\Carbon;
use function Tests\configureExplorerDatabase;

it('should make an instance that has all properties', function () {
    configureExplorerDatabase();

    $subject = new Slot([
        'order'        => 5,
        'wallet'       => new WalletViewModel(Wallet::factory()->create()),
        'forging_at'   => Carbon::now(),
        'last_block'   => [],
        'is_success'   => false,
        'is_warning'   => false,
        'is_danger'    => false,
        'missed_count' => 10,
        'status'       => 'next',
        'time'         => time(),
    ]);

    expect($subject->order())->toBeInt();
    expect($subject->wallet())->toBeInstanceOf(WalletViewModel::class);
    expect($subject->forgingAt())->toBeInstanceOf(Carbon::class);
    expect($subject->lastBlock())->toBeArray();
    expect($subject->isSuccess())->toBeBool();
    expect($subject->isWarning())->toBeBool();
    expect($subject->isDanger())->toBeBool();
    expect($subject->missedCount())->toBeInt();
    expect($subject->status())->toBeString();
    expect($subject->time())->toBeInt();
});
