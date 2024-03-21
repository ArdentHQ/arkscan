<?php

declare(strict_types=1);

use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use App\Services\Monitor\ValidatorTracker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use function Spatie\Snapshots\assertMatchesSnapshot;

beforeEach(function () {
    $this->activeValidators = require dirname(dirname(dirname(__DIR__))).'/fixtures/forgers.php';
    $this->expected         = require dirname(dirname(dirname(__DIR__))).'/fixtures/validator-tracker.php';
});

it('should calculate the forging order', function () {
    $this->travelTo(new Carbon('2021-01-01 00:04:00'));

    Wallet::factory(51)->create()->each(function ($wallet) {
        Round::factory()->create([
            'round'      => '112168',
            'public_key' => $wallet->public_key,
        ]);

        Cache::tags(['validators'])->put($wallet->public_key, $wallet);
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

    assertMatchesSnapshot(ValidatorTracker::execute($this->activeValidators, 5720517));
});

it('should get active validators', function () {
    $method = new ReflectionMethod(ValidatorTracker::class, 'getActiveValidators');
    $method->setAccessible(true);

    expect($method->invokeArgs(null, [$this->activeValidators]))
        ->toEqual($this->expected['input']);
});

it('should shuffle validators correctly', function () {
    $validatorsMethod = new ReflectionMethod(ValidatorTracker::class, 'getActiveValidators');
    $validatorsMethod->setAccessible(true);

    $activeValidators = $validatorsMethod->invokeArgs(null, [$this->activeValidators]);

    $shuffleMethod = new ReflectionMethod(ValidatorTracker::class, 'shuffleValidators');
    $shuffleMethod->setAccessible(true);

    expect($shuffleMethod->invokeArgs(null, [$activeValidators, 5720529]))
        ->toEqual($this->expected['output']);
});
