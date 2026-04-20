<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\PeersSyncer;
use Illuminate\Console\Command;

final class SyncPeers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'explorer:sync-peers {--api= : Override the API base URL}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch peers from the API and resolve their geo coordinates.';

    public function handle(): void
    {
        /** @var string|null $apiUrl */
        $apiUrl = $this->option('api');

        $count = (new PeersSyncer($apiUrl))->sync();

        $this->info(sprintf('Synced %d new peer(s).', $count));
    }
}
