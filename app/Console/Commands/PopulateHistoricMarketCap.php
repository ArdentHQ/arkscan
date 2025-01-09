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
        $currencies = array_keys(config('currencies'));
        foreach ($currencies as $currency) {
            $currencyPath = database_path('/seeders/pricing/marketcap/'.$currency.'.json');
            if (! file_exists($currencyPath)) {
                $this->output->writeln('Currency file not found for '.$currency);

                continue;
            }

            $data = collect(json_decode(file_get_contents($currencyPath), true)['stats'])
                ->map(fn ($item) => ['timestamp' => $item[0] / 1000, 'value' => floatval($item[1])])
                ->sortBy('value');

            $atlValue = $data->first();
            $athValue = $data->last();

            $cache->setMarketCapAtl(strtoupper($currency), $atlValue['timestamp'], $atlValue['value']);
            $cache->setMarketCapAth(strtoupper($currency), $athValue['timestamp'], $athValue['value']);
        }
    }
}
