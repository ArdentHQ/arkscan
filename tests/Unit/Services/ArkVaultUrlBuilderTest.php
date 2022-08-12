<?php

declare(strict_types=1);

use App\Services\ArkVaultUrlBuilder;
use Ardenthq\UrlBuilder\UrlBuilder;
use Illuminate\Support\Facades\Config;

it('creates an instance of the url builder', function () {
    expect(ArkVaultUrlBuilder::get())->toBeInstanceOf(UrlBuilder::class);
});

it('uses devnet network if explorer is set to development', function () {
    Config::set('explorer.network', 'development');

    $builder = ArkVaultUrlBuilder::get();

    expect($builder->nethash())->toBe('2a44f340d76ffc3df204c5f38cd355b7496c9065a1ade2ef92071436bd72e867');
});

it('uses mainnet network if explorer is set to production', function () {
    Config::set('explorer.network', 'production');

    $builder = ArkVaultUrlBuilder::get();

    expect($builder->nethash())->toBe('6e84d08bd299ed97c212c886c98a57e36545c8f5d645ca7eeae63a8bd62d8988');
});

