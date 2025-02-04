<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Receipt;
use App\Services\Cache\ContractCache;
use Illuminate\Console\Command;

class CacheContractAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-contract-addresses';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache contract addresses';

    public function handle(): void
    {
        $addresses = Receipt::where('deployed_contract_address', '!=', null)
            ->distinct()
            ->pluck('deployed_contract_address')
            ->toArray();

        (new ContractCache())->setContractAddresses($addresses);
    }
}
