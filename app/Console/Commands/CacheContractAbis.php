<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ContractAbiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

final class CacheContractAbis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-contract-abis';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild the cached contract method signatures from php-crypto ABIs.';

    public function handle(ContractAbiService $service): void
    {
        Cache::forget(ContractAbiService::CACHE_KEY);

        $signatures = $service->getAllSignatures();

        $this->info(sprintf('Cached %d contract method signatures.', count($signatures)));
    }
}
