<?php

namespace App\Console\Commands;

use App\Jobs\IndexModels;
use App\Jobs\IndexTransactions;
use App\Jobs\IndexWallets;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ScoutIndexModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scout:index-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update recently created laravel scout models';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        if (Cache::get('scout_indexing_paused_' . Transaction::class)) {
            $this->info(sprintf('Indexing is paused for "%s". Use the command scout:resume-indexing to resume indexing.', Transaction::class));
        } else {
            $this->info(sprintf('Indexing "%s" models.', Transaction::class));
            IndexTransactions::dispatch();
        }

        if (Cache::get('scout_indexing_paused_' . Wallet::class)) {
            $this->info(sprintf('Indexing is paused for "%s". Use the command scout:resume-indexing to resume indexing.', Wallet::class));
        } else {
            $this->info(sprintf('Indexing "%s" models.', Wallet::class));
            // IndexWallets::dispatch();
        }

        if (Cache::get('scout_indexing_paused_' . Block::class)) {
            $this->info(sprintf('Indexing is paused for "%s". Use the command scout:resume-indexing to resume indexing.', Block::class));
        } else {
            $this->info(sprintf('Indexing "%s" models.', Block::class));
            // IndexBlocks::dispatch();
        }

        return Command::SUCCESS;
    }
}
