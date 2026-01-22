<?php

declare(strict_types=1);

namespace App\Console\Commands\Webhooks;

use App\Models\Webhook;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

final class DeleteWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:webhook:delete {--host=} {--port=} {--token=} {--id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete ARK webhook';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /** @var string|null $token */
        $token = $this->option('token');

        /** @var string|null $id */
        $id = $this->option('id');

        if ($token === null && $id === null) {
            $this->error('Missing [token] or [id] argument.');

            return Command::FAILURE;
        }

        $webhook = Webhook::find($id);
        if ($webhook === null) {
            $webhook = Webhook::firstWhere('token', $token);
        }

        if ($webhook === null) {
            $this->error('Webhook does not exist.');

            return Command::FAILURE;
        }

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

        try {
            $response = Http::delete(sprintf(
                'http://%s:%d/api/webhooks/%s',
                $coreHost,
                $corePort,
                $webhook->id
            ));

            $data = json_decode($response->body(), true);
            if ($data !== null || str_starts_with((string) $response->status(), '2') === false) {
                $this->info(serialize($data));
                $this->error(sprintf(
                    'There was a problem removing the webhook: %s',
                    Arr::get($data, 'message', 'Unknown')
                ));

                return Command::FAILURE;
            }

            $webhook->delete();

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Could not connect to core webhooks endpoint: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
