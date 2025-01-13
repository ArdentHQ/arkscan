<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Price;
use App\Services\Cache\StatisticsCache;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

final class PopulateHistoricPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:populate-historic-prices';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Populate historic prices for currencies.';

    public function handle(StatisticsCache $cache): void
    {
        /** @var string[] $currencies */
        $currencies = array_keys(config('currencies'));

        foreach ($currencies as $currency) {
            $currencyPath = database_path('/seeders/pricing/prices/'.$currency.'.json');
            if (! file_exists($currencyPath)) {
                $this->output->writeln('Currency file not found for '.$currency);

                continue;
            }

            /** @var string $fileContent */
            $fileContent = file_get_contents($currencyPath);

            /** @var array{stats:array{0:int, 1:float}[]} $jsonData */
            $jsonData = json_decode($fileContent, true);

            $capitalisedCurrency = strtoupper($currency);

            /** @var Collection $data */
            $data = collect($jsonData['stats'])
                ->map(fn ($item) => [
                    'timestamp' => Carbon::createFromTimestampMs($item[0])->format('Y-m-d 00:00:00'),
                    'currency' => $capitalisedCurrency,
                    'value' => floatval($item[1]),
                ]);

            if ($data->last()['timestamp'] === $data->get($data->count() - 2)['timestamp']) {
                $data->pop();
            }

            Price::upsert($data->toArray(), ['timestamp', 'currency'], ['value']);
        }
    }
}
