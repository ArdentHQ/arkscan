<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Wallets;
use App\Jobs\CacheUsername;
use Illuminate\Console\Command;

final class CacheDelegateUsernames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-delegate-usernames';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache all usernames by their address and public key.';

    public function handle(): void
    {
        Wallets::allWithUsername()
            ->cursor()
            ->each(fn ($wallet) => CacheUsername::dispatch($wallet->toArray())->onQueue('usernames'));
    }
}
