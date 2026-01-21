<?php

declare(strict_types=1);

namespace App\Console\Commands\Webhooks;

use App\Models\Webhook;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

final class FlushWebhooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:webhook:flush {--host=} {--port=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove all webhooks';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach (Webhook::all() as $webhook) {
            /** @var string|null $coreHost */
            $coreHost = $this->option('host');

            /** @var string|null $corePort */
            $corePort = $this->option('port');

            if ($coreHost === null) {
                $coreHost = $webhook->host;
            }

            if ($corePort === null) {
                $corePort = $webhook->port;
            }

            Artisan::call('ark:webhook:delete', [
                '--host' => $coreHost,
                '--port' => $corePort,
                '--id'   => $webhook->id,
            ], $this->output->getOutput());
        }

        return Command::SUCCESS;
    }
}
