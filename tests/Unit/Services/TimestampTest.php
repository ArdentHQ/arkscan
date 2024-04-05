<?php

declare(strict_types=1);

use App\Facades\Network;
use App\Services\Timestamp;
use ARKEcosystem\Foundation\UserInterface\Support\DateFormat;
use Carbon\Carbon;

it('should create a Carbon object since genesis', function () {
    expect(Timestamp::fromGenesis(113160952))->toEqual(Network::epoch()->addSeconds(113160952));
});

it('should create a UNIX timestamp and format it', function () {
    expect(Timestamp::fromGenesisHuman(113160952))->toBe(Network::epoch()->addSeconds(113160952)->format(DateFormat::TIME));
});

it('should create a GENESIS timestamp', function () {
    expect(Timestamp::fromUnix(1603262152))->toEqual(Carbon::createFromTimestamp(1603262152));
});

it('should create a GENESIS timestamp and format it', function () {
    expect(Timestamp::fromUnixHuman(1603262152))->toBe('21 Oct 2020 06:35:52');
});
