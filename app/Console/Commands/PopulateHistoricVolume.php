<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\Cache\StatisticsCache;
use Illuminate\Console\Command;

final class PopulateHistoricVolume extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:populate-historic-volume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate historic volume values for currencies.';

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

            /** @var string $fileContent */
            $fileContent = file_get_contents($currencyPath);

            /** @var array{total_volumes:array{0:int, 1:float}[]} $jsonData */
            $jsonData = json_decode($fileContent, true);

            $data = collect($jsonData['total_volumes'])
                ->map(fn ($item) => ['timestamp' => $item[0] / 1000, 'value' => floatval($item[1])])
                ->sortBy('value');

            /** @var array{timestamp: int, value: float} $atlValue */
            $atlValue = $data->first();
            /** @var array{timestamp: int, value: float} $athValue */
            $athValue = $data->last();

            $cache->setVolumeAtl(strtoupper($currency), $atlValue['timestamp'], $atlValue['value']);
            $cache->setVolumeAth(strtoupper($currency), $athValue['timestamp'], $athValue['value']);
        }
    }
}
