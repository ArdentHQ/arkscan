<?php

declare(strict_types=1);

use App\Services\Identity;

it('should derive the address from the given public key', function () {
    expect(Identity::address('03d3fdad9c5b25bf8880e6b519eb3611a5c0b31adebc8455f0e096175b28321aff'))->toBe('0x6E4C6817a95263B758bbC52e87Ce8e759eD0B084');
});
