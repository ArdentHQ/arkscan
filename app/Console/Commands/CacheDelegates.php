<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

final class CacheDelegates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delegates:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache all delegates by their public key to avoid expensive database queries.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Wallet::whereNotNull('attributes->delegate->username')->orderBy('balance')->chunk(200, function ($wallets): void {
            foreach ($wallets as $wallet) {
                Cache::tags(['delegates'])->put($wallet->public_key, $wallet);
            }
        });
    }
}
