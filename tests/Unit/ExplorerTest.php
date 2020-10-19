<?php

declare(strict_types=1);

use App\Explorer;

it('should have a network name', function () {
    expect(Explorer::network())->toBeString();
});

it('should fetch the price for the given pair', function () {
    expect(Explorer::usesMarketsquare())->toBeBool();
});
