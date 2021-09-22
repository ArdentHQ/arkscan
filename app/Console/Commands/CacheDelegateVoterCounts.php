<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Wallets;
use App\Jobs\CacheVoterCountByPublicKey;
use Illuminate\Console\Command;

final class CacheDelegateVoterCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-delegate-voter-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache the voter count for each delegate.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        Wallets::allWithUsername()
            ->orderBy('balance')
            ->each(fn ($wallet) => CacheVoterCountByPublicKey::dispatch($wallet->public_key)->onQueue('voters'));
    }
}
