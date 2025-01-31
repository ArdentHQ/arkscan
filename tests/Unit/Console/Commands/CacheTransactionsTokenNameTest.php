<?php

declare(strict_types=1);

use App\Console\Commands\CacheTransactionsTokenName;
use App\Models\Receipt;
use App\Models\Transaction;
use App\Services\Cache\ContractCache;
use Illuminate\Support\Facades\Http;
use function Tests\faker;

it('should execute the command', function () {
    Http::fake([
        '*' => Http::response(['result' => '0x000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000000064441524b32300000000000000000000000000000000000000000000000000000'], 200),
    ]);

    $transaction = Transaction::factory()->contractDeployment()->create();

    $contractAddress = faker()->wallet['address'];

    Receipt::factory()->create([
        'id'                        => $transaction->id,
        'deployed_contract_address' => $contractAddress,
    ]);

    $cache = new ContractCache();

    (new CacheTransactionsTokenName())->handle($cache);

    expect($cache->getTokenName($contractAddress))->toBe('DARK20');
});

it('should not re-cache token name', function () {
    $cache = new ContractCache();

    $contractAddress = faker()->wallet['address'];

    $transaction = Transaction::factory()->contractDeployment()->create();

    Receipt::factory()->create([
        'id'                        => $transaction->id,
        'deployed_contract_address' => $contractAddress,
    ]);

    $cache->setTokenName($contractAddress, 'TESTTOKEN');

    (new CacheTransactionsTokenName())->handle($cache);

    expect($cache->getTokenName($contractAddress))->toBe('TESTTOKEN');
});

it('should handle transaction without a receipt', function () {
    Transaction::factory()->contractDeployment()->create();

    $contractAddress = faker()->wallet['address'];

    $cache = new ContractCache();

    (new CacheTransactionsTokenName())->handle($cache);

    expect($cache->getTokenName($contractAddress))->toBeNull();
});

it('should handle transaction without a deployed_contract_address', function () {
    $transaction = Transaction::factory()->contractDeployment()->create();

    Receipt::factory()->create([
        'id'                        => $transaction->id,
        'deployed_contract_address' => null,
    ]);

    $contractAddress = faker()->wallet['address'];

    $cache = new ContractCache();

    (new CacheTransactionsTokenName())->handle($cache);

    expect($cache->getTokenName($contractAddress))->toBeNull();
});

it('should handle no response from api', function () {
    Http::fake([
        '*' => Http::response(['result' => null], 200),
    ]);

    $transaction = Transaction::factory()->contractDeployment()->create();

    $contractAddress = faker()->wallet['address'];

    Receipt::factory()->create([
        'id'                        => $transaction->id,
        'deployed_contract_address' => $contractAddress,
    ]);

    $cache = new ContractCache();

    (new CacheTransactionsTokenName())->handle($cache);

    expect($cache->getTokenName($contractAddress))->toBeNull();
});
