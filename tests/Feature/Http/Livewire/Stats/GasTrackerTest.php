<?php

declare(strict_types=1);

use App\Http\Livewire\Stats\GasTracker;
use App\Services\BigNumber;
use App\Services\Cache\MainsailCache;
use Livewire\Livewire;

it('should render', function () {
    Livewire::test(GasTracker::class)
        ->assertViewHas([
            'lowFee'     => ['amount' => BigNumber::zero(), 'duration' => 8],
            'averageFee' => ['amount' => BigNumber::zero(), 'duration' => 8],
            'highFee'    => ['amount' => BigNumber::zero(), 'duration' => 8],
        ]);
});

it('should render with cached data', function () {
    (new MainsailCache())->setFees([
        'min' => '1500000000',
        'avg' => '2500000000',
        'max' => '3500000000',
    ]);

    Livewire::test(GasTracker::class)
        ->assertViewHas([
            'lowFee'     => ['amount' => BigNumber::new(1.5), 'duration' => 8],
            'averageFee' => ['amount' => BigNumber::new(2.5), 'duration' => 8],
            'highFee'    => ['amount' => BigNumber::new(3.5), 'duration' => 8],
        ])
        ->assertSeeInOrder([
            'Low',
            '~ 8 secs',
            '1.5',
            'Gwei',
            'Average',
            '~ 8 secs',
            '2.5',
            'Gwei',
            'High',
            '~ 8 secs',
            '3.5',
            'Gwei',
        ]);
});
