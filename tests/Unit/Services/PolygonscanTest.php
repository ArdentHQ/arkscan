<?php

declare(strict_types=1);

use App\Services\Polygonscan;

it('can generate url', function () {
    expect(Polygonscan::url('test-address'))->toBe('https://mumbai.polygonscan.com/address/test-address');
});

it('trims the trailing slash in the url', function () {
    expect(Polygonscan::url('test-address'))->toBe('https://mumbai.polygonscan.com/address/test-address');
});
