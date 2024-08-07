<?php

declare(strict_types=1);

use App\DTO\Statistics\DelegateStatistics;
use App\DTO\Statistics\WalletWithValue;
use App\Models\Wallet;
use Carbon\Carbon;

it('should convert to and from wireable array', function () {
    $oldestDate = Carbon::parse('2023-04-01 12:44:15');
    $newestDate = Carbon::parse('2024-04-01 12:44:15');

    $mostUniqueVotersWallet  = Wallet::factory()->create()->fresh();
    $leastUniqueVotersWallet = Wallet::factory()->create()->fresh();
    $mostBlocksForgedWallet  = Wallet::factory()->create()->fresh();

    $oldestActiveDelegateWallet = Wallet::factory()->create()->fresh();
    $newestActiveDelegateWallet = Wallet::factory()->create()->fresh();

    $oldestActiveDelegate = WalletWithValue::make($oldestActiveDelegateWallet, $oldestDate);
    $newestActiveDelegate = WalletWithValue::make($newestActiveDelegateWallet, $newestDate);

    $subject = DelegateStatistics::make(
        $mostUniqueVotersWallet,
        $leastUniqueVotersWallet,
        $mostBlocksForgedWallet,
        $oldestActiveDelegate,
        $newestActiveDelegate,
    );

    expect($subject->toLivewire())->toBe([
        'mostUniqueVoters'  => $mostUniqueVotersWallet->address,
        'leastUniqueVoters' => $leastUniqueVotersWallet->address,
        'mostBlocksForged'  => $mostBlocksForgedWallet->address,

        'oldestActiveDelegate' => [
            'wallet'    => $oldestActiveDelegateWallet->address,
            'timestamp' => $oldestDate->toISOString(),
        ],
        'newestActiveDelegate' => [
            'wallet'    => $newestActiveDelegateWallet->address,
            'timestamp' => $newestDate->toISOString(),
        ],
    ]);

    $subject = DelegateStatistics::fromLivewire($subject->toLivewire());

    expect($subject->mostUniqueVoters->address())->toEqual($mostUniqueVotersWallet->address);
    expect($subject->leastUniqueVoters->address())->toEqual($leastUniqueVotersWallet->address);
    expect($subject->mostBlocksForged->address())->toEqual($mostBlocksForgedWallet->address);
    expect($subject->oldestActiveDelegate)->toEqual($oldestActiveDelegate);
    expect($subject->newestActiveDelegate)->toEqual($newestActiveDelegate);
});
