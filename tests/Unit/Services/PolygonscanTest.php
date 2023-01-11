<?php

declare(strict_types=1);

use App\Services\Polygonscan;

it('can generate url', function () {
    config([
        'explorer.polygonscan_url' => 'https://polygonscan.com',
    ]);

    expect(Polygonscan::url('test-address'))->toBe('https://polygonscan.com/address/test-address');
});

it('trims the trailing slash in the url', function () {
    config([
        'explorer.polygonscan_url' => 'https://mumbai.polygonscan.com/',
    ]);

    expect(Polygonscan::url('test-address'))->toBe('https://mumbai.polygonscan.com/address/test-address');
});
