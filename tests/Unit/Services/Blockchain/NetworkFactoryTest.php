<?php

declare(strict_types=1);

use App\Services\Blockchain\Network;
use App\Services\Blockchain\NetworkFactory;

it('should handle ARK Production', function () {
    expect(NetworkFactory::make('production'))->toBeInstanceOf(Network::class);
});

it('should handle ARK Development', function () {
    expect(NetworkFactory::make('development'))->toBeInstanceOf(Network::class);
});

it('should throw if an unknown network is used', function () {
    NetworkFactory::make('unknown');
})->throws(InvalidArgumentException::class);
