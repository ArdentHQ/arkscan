<?php

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Http;

class IndexTransactions implements ShouldQueue
{
    // We should look into using the chunk so its likely the jobs take less than
    // 60 seconds to run, the limit is likely to be reached only on the first run
    const CHUNK_SIZE = 1_000_000;

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
        $latestIndexedTimestamp = $this->getLatestIndexedTimestamp();

        // If there are no indexed transactions yet we should not index anything
        // and wait for the user to run `php artisan scout:import "App\Models\Transaction"`
        // for the first time
        if ($latestIndexedTimestamp === null) {
            return;
        }

        $builder = Transaction::where('timestamp', '>', $latestIndexedTimestamp)
            ->orderBy('timestamp', 'asc')
            ->limit(self::CHUNK_SIZE);

        $builder->searchable();
    }

    private function getLatestIndexedTimestamp()
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . config('scout.meilisearch.key'),
        ])->post(config('scout.meilisearch.host') . '/indexes/transactions/search', [
            'q' => '*',
            'limit' => 1,
            'attributesToRetrieve' => ['timestamp'],
            'sort' => ['timestamp:desc'],
        ]);

        return $response->json('hits.0.timestamp');
    }
}
