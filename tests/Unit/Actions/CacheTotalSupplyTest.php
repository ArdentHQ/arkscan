<?php

declare(strict_types=1);

use App\Actions\CacheTotalSupply;
use App\Models\State;
use App\Services\Cache\NetworkCache;

it('should execute the command', function () {
    State::create([
        'id'           => 1,
        'block_number' => 1000,
        'supply'       => 120.0 * 1e18,
    ]);

    CacheTotalSupply::execute();

    expect((new NetworkCache())->getTotalSupply())->toBe(120.0);
});
