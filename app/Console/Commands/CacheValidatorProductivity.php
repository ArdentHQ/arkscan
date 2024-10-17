<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Facades\Rounds;
use App\Jobs\CacheProductivityByAddress;
use Illuminate\Console\Command;

final class CacheValidatorProductivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-validator-productivity';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Calculate and cache the productivity for each active validator.';

    public function handle(): void
    {
        collect(Rounds::current()->validators)
            ->each(function ($address) {
                return (bool) CacheProductivityByAddress::dispatch($address)->onQueue('productivity');
            });
    }
}
