<?php

use App\Http\Livewire\Stats\GasTracker;
use App\Services\Cache\MainsailCache;
use Livewire\Livewire;

it('should render', function () {
    Livewire::test(GasTracker::class)
        ->assertViewHas([
            'lowFee'     => ['amount' => 0.0, 'duration' => 8],
            'averageFee' => ['amount' => 0.0, 'duration' => 8],
            'highFee'    => ['amount' => 0.0, 'duration' => 8],
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
            'lowFee'     => ['amount' => 1.5, 'duration' => 8],
            'averageFee' => ['amount' => 2.5, 'duration' => 8],
            'highFee'    => ['amount' => 3.5, 'duration' => 8],
        ]);
});

it('should show gas details', function ($title, $amount) {
    (new MainsailCache())->setFees([
        'min' => '1500000000',
        'avg' => '2500000000',
        'max' => '3500000000',
    ]);

    Livewire::test(GasTracker::class)
        ->assertSeeInOrder([
            $title,
            '~ 8 secs',
            $amount,
            'Gwei',
        ]);
})->with([
    'low' => ['Low', '1.5'],
    'avg' => ['Average', '2.5'],
    'max' => ['High', '3.5'],
]);
