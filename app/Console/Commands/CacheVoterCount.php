<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Wallets;
use App\Jobs\CacheVoterCountByPublicKey;
use Illuminate\Console\Command;

final class CacheVoterCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:voter-count';

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
        Wallets::allWithUsername()->each(fn ($wallet) => CacheVoterCountByPublicKey::dispatch($wallet->public_key));
    }
}
