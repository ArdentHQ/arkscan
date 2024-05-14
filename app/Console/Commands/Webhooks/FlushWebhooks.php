<?php

declare(strict_types=1);

namespace App\Console\Commands\Webhooks;

use App\Models\Webhook;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

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
     * @var string|null
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

            $response = Http::delete(sprintf(
                'http://%s:%d/api/webhooks/%s',
                $coreHost,
                $corePort,
                $webhook->id
            ));

            $data = json_decode($response->body(), true);
            if ($data !== null) {
                $this->error(sprintf(
                    'There was a problem removing webhook [%s]: %s',
                    $webhook->id,
                    Arr::get($data, 'message', 'Unknown')
                ));
            }

            $webhook->delete();
        }

        return Command::SUCCESS;
    }
}
