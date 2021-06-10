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

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $height = intval($this->option('height'));
        $days   = floatval($this->option('days'));
        (new Job($height, $days))->handle();
    }
}
