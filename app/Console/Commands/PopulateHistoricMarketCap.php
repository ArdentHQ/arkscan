<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Cache\StatisticsCache;
use Illuminate\Console\Command;

final class PopulateHistoricMarketCap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:populate-historic-market-cap';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Populate historic market cap values for currencies.';

    public function handle(StatisticsCache $cache): void
    {
        /** @var string[] $currencies */
        $currencies = array_keys(config('currencies'));

        foreach ($currencies as $currency) {
            $currencyPath = database_path('/seeders/pricing/marketcap/'.$currency.'.json');
            if (! file_exists($currencyPath)) {
                $this->output->writeln('Currency file not found for '.$currency);

                continue;
            }

            $fileContent = file_get_contents($currencyPath);
            if ($fileContent === false) {
                $this->output->writeln('Failed to read currency file for '.$currency);

                continue;
            }

            /** @var array{stats:array{0:int, 1:float}[]} $jsonData */
            $jsonData = json_decode($fileContent, true);

            $data = collect($jsonData['stats'])
                ->map(fn ($item) => ['timestamp' => $item[0] / 1000, 'value' => floatval($item[1])])
                ->sortBy('value');

            /** @var array{timestamp: int, value: float} $atlValue */
            $atlValue = $data->first();
            /** @var array{timestamp: int, value: float} $athValue */
            $athValue = $data->last();

            $cache->setMarketCapAtl(strtoupper($currency), $atlValue['timestamp'], $atlValue['value']);
            $cache->setMarketCapAth(strtoupper($currency), $athValue['timestamp'], $athValue['value']);
        }
    }
}
