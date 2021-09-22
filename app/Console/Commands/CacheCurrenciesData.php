<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\MarketDataProvider;
use App\DTO\MarketData;
use App\Facades\Network;
use App\Services\Cache\NetworkStatusBlockCache;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;

final class CacheCurrenciesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-currencies-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache currencies data';

    public function handle(NetworkStatusBlockCache $cache, MarketDataProvider $marketDataProvider): void
    {
        if (! Network::canBeExchanged()) {
            return;
        }

        $baseCurrency     = Network::currency();
        $targetCurrencies = collect(config('currencies'))->pluck('currency');

        try {
            $priceAndPriceChange = $marketDataProvider->priceAndPriceChange($baseCurrency, $targetCurrencies);

            $priceAndPriceChange->each(function (MarketData $dto, string $currency) use ($baseCurrency, $cache) : void {
                $cache->setPrice($baseCurrency, $currency, $dto->price());
                $cache->setPriceChange($baseCurrency, $currency, $dto->priceChange());
            });
        } catch (ConnectionException $e) {
            $targetCurrencies->each(function ($currency) use ($baseCurrency, $cache) : void {
                $cache->setPrice($baseCurrency, $currency, null);
                $cache->setPriceChange($baseCurrency, $currency, null);
            });
        }
    }
}
