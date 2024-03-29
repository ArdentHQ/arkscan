<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Wallets;
use App\Jobs\CacheMultiSignatureAddress;
use App\Models\Wallet;
use Illuminate\Console\Command;

final class CacheMultiSignatureAddresses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-multi-signature-addresses';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache all multi-signature addresses.';

    public function handle(): void
    {
        Wallets::allWithMultiSignature()
            ->cursor()
            ->each(function ($wallet) {
                /** @var Wallet $wallet */
                CacheMultiSignatureAddress::dispatch($wallet->toArray())->onQueue('musig');
            });
    }
}
