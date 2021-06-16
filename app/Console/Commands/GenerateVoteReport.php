<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Jobs\GenerateVoteReport as Job;
use Illuminate\Console\Command;

final class GenerateVoteReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:generate-vote-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Vote Report file.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        (new Job())->handle();
    }
}
