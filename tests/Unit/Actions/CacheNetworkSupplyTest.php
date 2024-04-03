<?php

declare(strict_types=1);

use App\Actions\CacheNetworkSupply;
use App\Models\Block;
use App\Models\State;
use App\Services\Cache\NetworkCache;

beforeEach(function () {
    State::create([
        'id'     => 1,
        'height' => 1000,
        'supply' => 120.0,
    ]);
});

it('should execute the command', function () {
    CacheNetworkSupply::execute();

    expect((new NetworkCache())->getSupply())->toBe(120.0);
});
