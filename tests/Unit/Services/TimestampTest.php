<?php

declare(strict_types=1);

use App\Services\Timestamp;

it('should create a UNIX timestamp', function () {
    expect(Timestamp::fromGenesis(113160952)->unix())->toBe(1603262152);
});

it('should create a UNIX timestamp and format it', function () {
    expect(Timestamp::fromGenesisHuman(113160952))->toBe('21 Oct 2020 06:35:52');
});

it('should create a GENESIS timestamp', function () {
    expect(Timestamp::fromUnix(1603262152)->unix())->toBe(113160952);
});

it('should create a GENESIS timestamp and format it', function () {
    expect(Timestamp::fromUnixHuman(1603262152))->toBe('21 Oct 2020 06:35:52');
});
