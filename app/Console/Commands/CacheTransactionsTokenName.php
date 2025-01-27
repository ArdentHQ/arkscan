<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ContractMethod;
use App\Models\Scopes\ContractDeploymentScope;
use App\Models\Transaction;
use App\Services\Cache\TokenTransferCache;
use ArkEcosystem\Crypto\Utils\AbiDecoder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

final class CacheTransactionsTokenName extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-transactions-token-name';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache the token name for token transfers.';

    public function handle(): void
    {
        $cache = new TokenTransferCache();

        $transactions = Transaction::withScope(ContractDeploymentScope::class, ContractMethod::transfer())->get();

        $transactions->each(function (Transaction $transaction) use ($cache) : void {
            if (! $cache->hasTokenName($transaction->id)) {
                // @TODO: Call a job to fetch specific token name?
                // dispatch(function () use ($transaction, $cache) : void {
                $tokenName = $this->fetchTokenName($transaction);

                dd($tokenName);

                $cache->setTokenName($transaction->id, $tokenName);
                // });

                $cache->setTokenName($transaction->id, $tokenName);
            }
        });
    }

    private function fetchTokenName(Transaction $transaction): string
    {
        $response = Http::withHeader('Content-Type', 'application/json')
            ->post('https://dwallets-evm.ihost.org/evm/api', [
            'jsonrpc' => '2.0',
            'method'  => 'eth_call',
            'params'  => [[
                'from' => '0x12361f0Bd5f95C3Ea8BF34af48F5484b811B5CCe',
                'to'   => $transaction->receipt->deployed_contract_address,
                'data' => '0x06fdde03',
            ], 'latest'],
            'id' => 1,
        ]);

        $payload = $response->json()['result'];
        // // $payload = '0x000000000000000000000000000000000000000000000000000000000000002000000000000000000000000000000000000000000000000000000000000000064441524b32300000000000000000000000000000000000000000000000000000';

        $method = (new AbiDecoder())->decodeFunctionWithAbi('function name() view returns (string)', $payload);

        return $method[0];
    }
}
