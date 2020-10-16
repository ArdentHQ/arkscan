<?php

declare(strict_types=1);

use App\Services\Blockchain\NetworkFactory;
use Illuminate\Support\Facades\Http;

it('should fetch known wallets', function () {
    Http::fake([
        'github.com' => [],
    ]);

    expect(NetworkFactory::make('ark.development')->knownWallets())->toBeArray();
    expect(NetworkFactory::make('ark.development')->knownWallets())->toHaveCount(0);
});
