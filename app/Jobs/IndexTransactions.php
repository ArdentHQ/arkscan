<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class IndexTransactions implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    // We should look into using the chunk so its likely the jobs take less than
    // 60 seconds to run, the limit is likely to be reached only on the first run
    public const CHUNK_SIZE = 1_000_000;

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

        info($latestIndexedTimestamp);

        $builder = Transaction::where('timestamp', '>', $latestIndexedTimestamp)
            ->orderBy('timestamp', 'asc')
            ->limit(self::CHUNK_SIZE);

        info('to index '.$builder->count().' transactions');

        $builder->searchable();
    }

    private function getLatestIndexedTimestamp()
    {
        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer '.config('scout.meilisearch.key'),
        ])->post(config('scout.meilisearch.host').'/indexes/transactions/search', [
            'q'                    => '*',
            'limit'                => 1,
            'attributesToRetrieve' => ['timestamp'],
            'sort'                 => ['timestamp:desc'],
        ]);

        return $response->json('hits.0.timestamp');
    }
}
