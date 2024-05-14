<?php

declare(strict_types=1);

namespace App\Console\Commands\Webhooks;

use App\Models\Webhook;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;

final class SetupWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:webhook:setup {--host=} {--port=4004} {--event=}';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'Setup ARK webhook';

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

        /** @var string|null $event */
        $event = $this->option('event');

        if ($coreHost === null) {
            $this->error('Missing [host] argument.');

            return Command::FAILURE;
        }

        if ($corePort === null) {
            $this->error('Missing [port] argument.');

            return Command::FAILURE;
        }

        if ($event === null) {
            $this->error('Missing [event] argument.');

            return Command::FAILURE;
        }

        $url = sprintf(
            'http://%s:%d/api/webhooks',
            $coreHost,
            $corePort
        );

        $response = Http::post($url, [
            'event'      => $event,
            'target'     => URL::signedRoute('webhooks'),
            'enabled'    => true,
            'conditions' => [],
        ]);

        $data = json_decode($response->body(), true);

        $this->info('ID: '.$data['data']['id']);
        $this->info('Token: '.$data['data']['token']);

        Webhook::create([
            'token' => $data['data']['token'],
            'host'  => $coreHost,
            'port'  => $corePort,
            'event' => $event,
        ]);

        return Command::SUCCESS;
    }
}
