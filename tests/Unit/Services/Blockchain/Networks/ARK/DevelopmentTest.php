<?php

declare(strict_types=1);

use App\Services\Blockchain\NetworkFactory;
use Illuminate\Support\Facades\Http;

it('should have a name', function () {
    expect(NetworkFactory::make('ark.production')->name())->toBe('ARK Development Network');
});

it('should have a symbol', function () {
    expect(NetworkFactory::make('ark.production')->symbol())->toBe('ARK');
});

it('should fetch known wallets', function () {
    Http::fake([
        'github.com' => [],
    ]);

    expect(NetworkFactory::make('ark.development')->knownWallets())->toBeArray();
    expect(NetworkFactory::make('ark.development')->knownWallets())->toHaveCount(0);
});

it('should determine if the network currency can be exchanged', function () {
    expect(NetworkFactory::make('ark.production')->canBeExchanged())->toBeFalse();
});
