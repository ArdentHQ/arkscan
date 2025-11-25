<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

final class ScoutPauseIndexing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:pause-indexing
        {model : Class name of model to pause indexing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pause laravel scout indexing';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $class = $this->argument('model');

        $model = new $class();

        Cache::forever('scout_indexing_paused_'.$model::class, true);

        return Command::SUCCESS;
    }
}
