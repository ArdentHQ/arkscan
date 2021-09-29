<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Facades\Network;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Symfony\Component\Process\Process;

final class GenerateVoteReport implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(): void
    {
        (new Process(['bash', resource_path('scripts/vote-report.sh'), Network::api()]))
            ->setTimeout(300)
            ->run();
    }
}
