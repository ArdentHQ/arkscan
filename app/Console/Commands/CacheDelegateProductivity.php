<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Rounds;
use App\Jobs\CacheProductivityByPublicKey;
use Illuminate\Console\Command;

final class CacheDelegateProductivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-delegate-productivity';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Calculate and cache the productivity for each active delegate.';

    public function handle(): void
    {
        collect(Rounds::current()->validators)
            ->each(function ($publicKey) {
                return (bool) CacheProductivityByPublicKey::dispatch($publicKey)->onQueue('productivity');
            });
    }
}
