<?php

declare(strict_types=1);

use App\Console\Commands\CacheNetworkAggregates;
use App\Console\Commands\CacheTransactionsTokenName;
use App\Models\Receipt;
use App\Models\Transaction;
use App\Services\Cache\NetworkCache;
use App\Services\Cache\TokenTransferCache;
use Illuminate\Support\Facades\Http;

use function Tests\faker;

const TOKEN_RESULT = '0x000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000000064441524b32300000000000000000000000000000000000000000000000000000';

it('should execute the command', function () {
    Http::fake([
        '*' => Http::response(['result' => TOKEN_RESULT], 200),
    ]);

    $transaction = Transaction::factory()->contractDeployment()->create();

    $contractAddress = faker()->wallet['address'];

    Receipt::factory()->create([
        'id' => $transaction->id,
        'deployed_contract_address' => $contractAddress,
    ]);

    $cache = new TokenTransferCache();

    (new CacheTransactionsTokenName())->handle($cache);

    expect($cache->getTokenName($contractAddress))->toBe('DARK20');
});

it('should not re-cache token name', function () {
    $cache = new TokenTransferCache();

    $contractAddress = faker()->wallet['address'];

    $transaction = Transaction::factory()->contractDeployment()->create();

    Receipt::factory()->create([
        'id' => $transaction->id,
        'deployed_contract_address' => $contractAddress,
    ]);

    $cache->setTokenName($contractAddress, 'TESTTOKEN');

    (new CacheTransactionsTokenName())->handle($cache);

    expect($cache->getTokenName($contractAddress))->toBe('TESTTOKEN');
});
