<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Cache;

it('caches contract ABI signatures and generates lang file', function () {
    Cache::forget('contract_abi_signatures');

    $this->artisan('explorer:cache-contract-abis')
        ->expectsOutputToContain('Cached')
        ->assertExitCode(0);

    expect(Cache::has('contract_abi_signatures'))->toBeTrue();

    $contracts = include resource_path('lang/en/contracts.php');

    expect($contracts)->toBeArray();
    expect($contracts)->toHaveKey('6dd7d8ea', 'vote(address)');
    expect($contracts)->toHaveKey('a9059cbb', 'transfer(address,uint256)');
    expect($contracts)->toHaveKey('argument', '[:index]: :value');
});
