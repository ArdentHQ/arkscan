<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Wallets;
use App\Jobs\CacheMultiSignatureAddress;
use Illuminate\Console\Command;

final class CacheMultiSignatureAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache all multi-signature addresses.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Wallets::allWithMultiSignature()
            ->cursor()
            ->each(fn ($wallet) => CacheMultiSignatureAddress::dispatch($wallet));
    }
}
