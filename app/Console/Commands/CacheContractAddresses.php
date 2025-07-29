<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Receipt;
use App\Services\Cache\WalletCache;
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
        $addresses = Receipt::where('contract_address', '!=', null)
            ->distinct()
            ->pluck('contract_address')
            ->toArray();

        (new WalletCache())->setContractAddresses($addresses);
    }
}
