<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\CacheResignationIds;
use Illuminate\Console\Command;

final class CacheValidatorResignationIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:cache-validator-resignation-ids';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Cache all transaction IDs for validator resignations.';

    public function handle(): void
    {
        CacheResignationIds::dispatch()->onQueue('resignations');
    }
}
