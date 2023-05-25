<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\FetchExchangeDetails;
use App\Models\Exchange;
use Illuminate\Console\Command;

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
        Exchange::coingecko()->each(function ($exchange) {
            FetchExchangeDetails::dispatch($exchange);
        });

        return Command::SUCCESS;
    }
}
