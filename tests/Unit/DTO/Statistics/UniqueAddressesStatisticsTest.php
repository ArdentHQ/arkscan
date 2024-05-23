<?php

use App\DTO\Statistics\UniqueAddressesStatistics;
use App\Models\Wallet;
use Carbon\Carbon;

it('should convert to and from wireable array', function () {
    $genesis = Wallet::factory()->create();
    $newest = Wallet::factory()->create();
    $mostTransactions = Wallet::factory()->create();
    $largest = Wallet::factory()->create();

    $genesisData = [
        'address' => $genesis->address,
        'value' => Carbon::parse('2024-05-01 12:44:01'),
    ];

    $newestData = [
        'address' => $newest->address,
        'timestamp' => Carbon::parse('2024-04-01 12:44:01')->timestamp,
        'value' => Carbon::parse('2024-04-01 12:44:01'),
    ];

    $mostTransactions = [
        'address' => $mostTransactions->address,
        'value' => Carbon::parse('2024-03-01 12:44:01')->timestamp,
    ];

    $largestData = [
        'address' => $largest->address,
        'value' => '123.456 DARK',
    ];

    $subject = UniqueAddressesStatistics::make(
        $genesisData,
        $newestData,
        $mostTransactions,
        $largestData,
    );

    expect($subject->toLivewire())->toBe([
        'genesis' => $genesisData,
        'newest' => $newestData,
        'mostTransactions' => $mostTransactions,
        'largest' => $largestData,
    ]);

    $subject = UniqueAddressesStatistics::fromLivewire($subject->toLivewire());

    expect($subject->genesis)->toBe($genesisData);
    expect($subject->newest)->toBe($newestData);
    expect($subject->mostTransactions)->toBe($mostTransactions);
    expect($subject->largest)->toBe($largestData);
});
