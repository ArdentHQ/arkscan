<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\MarketDataProvider;
use App\Exceptions\CoinGeckoThrottledException;
use App\Models\Exchange;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

final class FetchExchangesDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exchanges:fetch-details';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Call the job that retrieves the volume and price for each compatible exchange';

    public function handle(): int
    {
        $exchanges = Exchange::coingecko()
            ->orderBy('volume', 'desc')
            ->get()
            ->filter(fn ($exchange) => $exchange->updated_at < Carbon::now()->subHours(1))
            ->sort(fn ($a, $b) => ($a->updated_at?->unix() ?? 0) - ($b->updated_at?->unix() ?? 0));

        foreach ($exchanges as $exchange) {
            try {
                $result = app(MarketDataProvider::class)->exchangeDetails($exchange);
            } catch (CoinGeckoThrottledException) {
                continue;
            }

            $exchange->price  = Arr::get($result, 'price', $exchange->price);
            $exchange->volume = Arr::get($result, 'volume', $exchange->volume);

            $exchange->save();

            // We touch to make sure the updated_at was changed to prevent unnecessary updates.
            $exchange->touch();
        }

        return Command::SUCCESS;
    }
}
