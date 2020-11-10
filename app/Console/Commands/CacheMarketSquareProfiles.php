<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Wallets;
use App\Jobs\CacheMarketSquareProfileByAddress;
use Illuminate\Console\Command;

final class CacheMarketSquareProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-market-square-profiles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache all MarketSquare profiles for delegates.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Wallets::allWithUsername()
            ->cursor()
            ->each(fn ($wallet) => CacheMarketSquareProfileByAddress::dispatch($wallet->toArray()));
    }
}
