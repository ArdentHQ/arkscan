<?php

declare(strict_types=1);

namespace App\Console\Commands\Webhooks;

use App\Models\Webhook;
use Illuminate\Console\Command;

final class ListWebhooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ark:webhook:list';

    /**
     * The console command description.
     *
     * @var string|null
     */
    protected $description = 'List ARK webhooks';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $webhooks = Webhook::all();
        if (count($webhooks) === 0) {
            $this->info('There are currently no webhooks.');

            return Command::SUCCESS;
        }

        $this->table(
            ['ID', 'Host', 'Event', 'Token'],
            $webhooks->map(function (Webhook $webhook) {
                return [
                    'id'    => $webhook->id,
                    'host'  => sprintf('%s:%d', $webhook->host, $webhook->port),
                    'event' => $webhook->event,
                    'token' => $webhook->token,
                ];
            })
        );

        return Command::SUCCESS;
    }
}
