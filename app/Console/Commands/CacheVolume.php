<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\MarketDataProvider;
use App\Exceptions\CoinGeckoThrottledException;
use App\Facades\Network;
use App\Services\Cache\CryptoDataCache;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CacheVolume extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-volume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache exchange volume for the active network currency';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CryptoDataCache $crypto, MarketDataProvider $marketDataProvider)
    {
        if (! Network::canBeExchanged()) {
            return;
        }

        /** @var array<string, array<string, string>> */
        $currencies = config('currencies');

        try {
            $result = $marketDataProvider->volume(Network::currency());

            if (! empty($result)) {
                collect($currencies)->values()->each(function ($currency) use ($crypto, $result): void {
                    $currency = $currency['currency'];
                    $volume   = $result[Str::lower($currency)];

                    $crypto->setVolume($currency, $volume);
                });
            }
        } catch (CoinGeckoThrottledException) {
            // Ignore and we'll try next time
        }

        return Command::SUCCESS;
    }
}
