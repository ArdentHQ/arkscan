<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Scopes\ContractDeploymentScope;
use App\Models\Transaction;
use App\Services\Cache\ContractCache;
use App\Services\MainsailApi;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;

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

    public function handle(ContractCache $cache): void
    {
        /** @var Collection<int, Transaction> $transactions */
        $transactions = Transaction::withScope(ContractDeploymentScope::class)->get();

        foreach ($transactions as $transaction) {
            if ($transaction->receipt === null) {
                continue;
            }

            if ($transaction->receipt->deployed_contract_address === null) {
                continue;
            }

            $contractAddress = $transaction->receipt->deployed_contract_address;
            if ($cache->hasTokenName($contractAddress)) {
                continue;
            }

            $tokenName = MainsailApi::deployedTokenName($contractAddress);
            if ($tokenName === null) {
                continue;
            }

            $cache->setTokenName($contractAddress, $tokenName);
        }
    }
}
