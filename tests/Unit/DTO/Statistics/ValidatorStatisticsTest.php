<?php

declare(strict_types=1);

use App\DTO\Statistics\ValidatorStatistics;
use App\DTO\Statistics\WalletWithValue;
use App\Models\Wallet;
use Carbon\Carbon;

it('should convert to and from wireable array', function () {
    $oldestDate = Carbon::parse('2023-04-01 12:44:15');
    $newestDate = Carbon::parse('2024-04-01 12:44:15');

    $mostUniqueVotersWallet  = Wallet::factory()->create()->fresh();
    $leastUniqueVotersWallet = Wallet::factory()->create()->fresh();
    $mostBlocksForgedWallet  = Wallet::factory()->create()->fresh();

    $oldestActiveValidatorWallet = Wallet::factory()->create()->fresh();
    $newestActiveValidatorWallet = Wallet::factory()->create()->fresh();

    $oldestActiveValidator = WalletWithValue::make($oldestActiveValidatorWallet, $oldestDate);
    $newestActiveValidator = WalletWithValue::make($newestActiveValidatorWallet, $newestDate);

    $subject = ValidatorStatistics::make(
        $mostUniqueVotersWallet,
        $leastUniqueVotersWallet,
        $mostBlocksForgedWallet,
        $oldestActiveValidator,
        $newestActiveValidator,
    );

    expect($subject->toLivewire())->toBe([
        'mostUniqueVoters'  => $mostUniqueVotersWallet->address,
        'leastUniqueVoters' => $leastUniqueVotersWallet->address,
        'mostBlocksForged'  => $mostBlocksForgedWallet->address,

        'oldestActiveValidator' => [
            'wallet'    => $oldestActiveValidatorWallet->address,
            'timestamp' => $oldestDate->toISOString(),
        ],
        'newestActiveValidator' => [
            'wallet'    => $newestActiveValidatorWallet->address,
            'timestamp' => $newestDate->toISOString(),
        ],
    ]);

    $subject = ValidatorStatistics::fromLivewire($subject->toLivewire());

    expect($subject->mostUniqueVoters->address())->toEqual($mostUniqueVotersWallet->address);
    expect($subject->leastUniqueVoters->address())->toEqual($leastUniqueVotersWallet->address);
    expect($subject->mostBlocksForged->address())->toEqual($mostBlocksForgedWallet->address);
    expect($subject->oldestActiveValidator)->toEqual($oldestActiveValidator);
    expect($subject->newestActiveValidator)->toEqual($newestActiveValidator);
});
