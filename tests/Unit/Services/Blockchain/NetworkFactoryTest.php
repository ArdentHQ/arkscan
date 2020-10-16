<?php

declare(strict_types=1);

use App\Services\Blockchain\NetworkFactory;
use App\Services\Blockchain\Networks\ARK\Development;
use App\Services\Blockchain\Networks\ARK\Production;

it('should handle ARK Production', function () {
    expect(NetworkFactory::make('ark.production'))->toBeInstanceOf(Production::class);
});

it('should handle ARK Development', function () {
    expect(NetworkFactory::make('ark.development'))->toBeInstanceOf(Development::class);
});
