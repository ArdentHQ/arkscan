<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Monitor\DelegateTracker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use function Spatie\Snapshots\assertMatchesSnapshot;

beforeEach(function () {
    $this->activeDelegates = require dirname(dirname(dirname(__DIR__))).'/fixtures/forgers.php';
    $this->expected        = require dirname(dirname(dirname(__DIR__))).'/fixtures/delegate-tracker.php';
});

it('should calculate the forging order', function () {
    $this->travelTo(new Carbon('2021-01-01 00:04:00'));

    Wallet::factory(51)->create()->each(function ($wallet) {
        Round::factory()->create([
            'round'      => '112168',
            'public_key' => $wallet->public_key,
        ]);

        Cache::tags(['delegates'])->put($wallet->public_key, $wallet);
        Cache::put('lastBlock:'.$wallet->public_key, []);
    });

    // Start height for round 112168
    Block::factory()->create([
        'height'    => 5720517,
        'timestamp' => 113620816,
    ]);

    Block::factory()->create([
        'height'    => 5720529,
        'timestamp' => 113620904,
    ]);

    assertMatchesSnapshot(DelegateTracker::execute($this->activeDelegates, 5720517));
});

it('should get active delegates', function () {
    $method = new ReflectionMethod(DelegateTracker::class, 'getActiveDelegates');
    $method->setAccessible(true);

    expect($method->invokeArgs(null, [$this->activeDelegates]))
        ->toEqual($this->expected['input']);
});

it('should shuffle delegates correctly', function () {
    $delegatesMethod = new ReflectionMethod(DelegateTracker::class, 'getActiveDelegates');
    $delegatesMethod->setAccessible(true);

    $activeDelegates = $delegatesMethod->invokeArgs(null, [$this->activeDelegates]);

    $shuffleMethod = new ReflectionMethod(DelegateTracker::class, 'shuffleDelegates');
    $shuffleMethod->setAccessible(true);

    expect($shuffleMethod->invokeArgs(null, [$activeDelegates, 5720529]))
        ->toEqual($this->expected['output']);
});
