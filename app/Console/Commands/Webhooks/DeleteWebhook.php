<?php

declare(strict_types=1);

namespace App\Console\Commands\Webhooks;

use App\Models\Webhook;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

final class DeleteWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:webhook:delete {--host=} {--port=4004} {--token=}';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Delete ARK webhook';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /** @var string|null $coreHost */
        $coreHost = $this->option('host');

        /** @var string|null $corePort */
        $corePort = $this->option('port');

        /** @var string|null $token */
        $token = $this->option('token');

        if ($coreHost === null) {
            $this->error('Missing [host] argument.');

            return Command::FAILURE;
        }

        if ($corePort === null) {
            $this->error('Missing [port] argument.');

            return Command::FAILURE;
        }

        if ($token === null) {
            $this->error('Missing [token] argument.');

            return Command::FAILURE;
        }

        $webhook = Webhook::find($token);
        if ($webhook === null) {
            $this->error('Token does not exist.');

            return Command::FAILURE;
        }

        $response = Http::delete(sprintf(
            'http://%s:%d/api/webhooks/%s',
            $coreHost,
            $corePort,
            $token
        ));

        $data = json_decode($response->body(), true);

        dd($data);

        return Command::SUCCESS;
    }
}
