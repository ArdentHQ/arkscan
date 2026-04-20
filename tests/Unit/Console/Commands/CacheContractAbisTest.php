<?php

declare(strict_types=1);

use App\Services\ContractAbiService;
use Illuminate\Support\Facades\Cache;

it('rebuilds the contract ABI signatures cache', function () {
    Cache::forget(ContractAbiService::CACHE_KEY);

    $this->artisan('explorer:cache-contract-abis')
        ->expectsOutputToContain('Cached')
        ->assertExitCode(0);

    expect(Cache::has(ContractAbiService::CACHE_KEY))->toBeTrue();

    $signatures = Cache::get(ContractAbiService::CACHE_KEY);
    expect($signatures)->toBeArray();
    expect($signatures)->toHaveKey('6dd7d8ea', 'vote(address)');
});
