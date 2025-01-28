<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\ContractMethod;
use App\Models\Scopes\ContractDeploymentScope;
use App\Models\Transaction;
use App\Services\Cache\TokenTransferCache;
use App\Services\MainsailApi;
use Illuminate\Console\Command;

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

    public function handle(TokenTransferCache $cache): void
    {
        $transactions = Transaction::withScope(ContractDeploymentScope::class, ContractMethod::transfer())->get();

        foreach ($transactions as $transaction) {
            $contractAddress = $transaction->receipt->deployed_contract_address;
            if ($cache->hasTokenName($contractAddress)) {
                continue;
            }

            $tokenName = MainsailApi::deployedTokenName($contractAddress);

            $cache->setTokenName($contractAddress, $tokenName);
        }
    }
}
