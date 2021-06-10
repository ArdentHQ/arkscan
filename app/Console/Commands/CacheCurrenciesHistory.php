<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Network;
use App\Jobs\CacheCurrenciesHistory as CacheCurrenciesHistoryJob;
use Illuminate\Console\Command;

final class CacheCurrenciesHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-currencies-history {--no-delay}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cache currencies history';

    public function handle(): void
    {
        if (! Network::canBeExchanged()) {
            return;
        }

        $source     = Network::currency();
        $currencies = collect(config('currencies'))->pluck('currency');

        $currencies->each(function ($currency, $index) use ($source): void {
            // Cache one currency history per-minute
            if ($this->option('no-delay') === true) {
                $delay = null;
            } else {
                $delay = now()->addMinutes($index);
            }

            CacheCurrenciesHistoryJob::dispatch($source, $currency)->delay($delay);
        });
    }
}
