<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\MainsailApi;
use Illuminate\Console\Command;

final class CacheGasTrackerData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:fetch-gas-tracker-data';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Fetch data from the Gas Tracker API and update the cache';

    public function handle(): void
    {
        MainsailApi::fees();
    }
}
