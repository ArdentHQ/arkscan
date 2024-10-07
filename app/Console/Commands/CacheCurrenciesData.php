<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\MarketDataProvider;
use App\DTO\MarketData;
use App\Facades\Network;
use App\Services\Cache\NetworkStatusBlockCache;
use Illuminate\Console\Command;

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
     * @var string|null
     */
    protected $description = 'Cache currencies data';

    public function handle(NetworkStatusBlockCache $cache, MarketDataProvider $marketDataProvider): void
    {
        if (! Network::canBeExchanged()) {
            return;
        }

        $baseCurrency = Network::currency();

        /** @var array<string, array<string, string>> */
        $allCurrencies = config('currencies.currencies');

        $targetCurrencies = collect($allCurrencies)->pluck('currency');

        $priceAndPriceChange = $marketDataProvider->priceAndPriceChange($baseCurrency, $targetCurrencies);

        $priceAndPriceChange->each(function (MarketData $dto, string $currency) use ($baseCurrency, $cache) : void {
            $cache->setPrice($baseCurrency, $currency, $dto->price());
            $cache->setPriceChange($baseCurrency, $currency, $dto->priceChange());
        });
    }
}
