<?php

declare(strict_types=1);

use App\Services\ArkVaultUrlBuilder;
use Ardenthq\UrlBuilder\UrlBuilder;
use Illuminate\Support\Facades\Config;

it('creates an instance of the url builder', function () {
    expect(ArkVaultUrlBuilder::get())->toBeInstanceOf(UrlBuilder::class);
});

it('uses devnet network if explorer is set to development', function () {
    Config::set('arkscan.network', 'development');

    $builder = ArkVaultUrlBuilder::get();

    expect($builder->nethash())->toBe(config('arkscan.networks.development.nethash'));
});

it('uses mainnet network if explorer is set to production', function () {
    Config::set('arkscan.network', 'production');

    $builder = ArkVaultUrlBuilder::get();

    expect($builder->nethash())->toBe(config('arkscan.networks.production.nethash'));
});
