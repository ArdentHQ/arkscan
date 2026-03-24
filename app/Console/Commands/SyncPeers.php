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
    protected $signature = 'explorer:sync-peers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch peers from the API and resolve their geo coordinates.';

    public function handle(PeersSyncer $syncer): void
    {
        $count = $syncer->sync();

        $this->info(sprintf('Synced %d new peer(s).', $count));
    }
}
