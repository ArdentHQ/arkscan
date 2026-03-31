<?php

declare(strict_types=1);

use App\Support\Broadcasting;
use Illuminate\Support\Facades\Config;

it('returns true when broadcasting default is reverb', function () {
    Config::set('broadcasting.default', 'reverb');

    expect(Broadcasting::usesWebSockets())->toBeTrue();
});

it('returns false when broadcasting default is not reverb', function () {
    Config::set('broadcasting.default', null);

    expect(Broadcasting::usesWebSockets())->toBeFalse();
});
