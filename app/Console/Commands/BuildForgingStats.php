<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\BuildForgingStats as Job;
use Illuminate\Console\Command;

final class BuildForgingStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:forging-stats:build {--height=} {--days=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Build forging stats into forging_stats database.';

    public function handle(): void
    {
        $height = (int) $this->option('height');
        $days   = (float) $this->option('days');
        (new Job($height, $days))->handle();
    }
}
