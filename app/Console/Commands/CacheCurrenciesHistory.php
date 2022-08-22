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

        $source = Network::currency();

        /** @var array<string, array<string, string>> */
        $allCurrencies = config('currencies');

        $currencies = collect($allCurrencies)->pluck('currency');

        $currencies->each(function ($currency, $index) use ($source): void {
            if ($this->option('no-delay') === true) {
                $delay = null;
            } else {
                // Spread out requests to avoid ratelimits, 2 per minute currently
                $delay = now()->addSeconds($index * 30);
            }

            CacheCurrenciesHistoryJob::dispatch($source, $currency)->delay($delay);
        });
    }
}
