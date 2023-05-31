<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;


class IndexWallets implements ShouldQueue
{
    // We should look into using the chunk so its likely the jobs take less than
    // 60 seconds to run, the limit is likely to be reached only on the first runs
    // until the index is up to date
    const CHUNK_SIZE = 100;

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     * @return void
     */
    public function handle()
    {
        $lastIndexedAt = $this->getLastIndexedAt();

        $builder = Wallet::limit(self::CHUNK_SIZE);

        $builder->searchable();

    }

    private function getLastIndexedAt()
    {
        $lastIndexed = DB::table('scout_indexing')->where('model', Wallet::class)->first();

        return $lastIndexed?->last_indexed_at;
    }
}
