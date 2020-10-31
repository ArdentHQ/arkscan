<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Wallets;
use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

final class CacheVotes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:votes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache wallets that have been voted for to avoid duplicate queries.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $publicKeys = Wallets::allWithVote()
            ->distinct('attributes->vote')
            ->pluck('attributes')
            ->pluck('vote')
            ->toArray();

        Wallet::whereIn('public_key', $publicKeys)
            ->get()
            ->each(fn ($wallet) => Cache::put('votes.'.$wallet->public_key, $wallet));
    }
}
