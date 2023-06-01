<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

abstract class IndexModel implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     * @return void
     */
    abstract public function handle(): void;

    abstract protected function elementsToIndexQuery(int $latestIndexedTimestamp): Builder;

    protected function execute(string $indexName): void
    {
        $latestIndexedTimestamp = $this->getLatestIndexedTimestamp($indexName);

        // If there are no indexed transactions yet we should not index anything
        // and wait for the user to run `php artisan scout:import "App\Models\Transaction"`
        // for the first time
        if ($latestIndexedTimestamp === null) {
            return;
        }

        $query = $this->elementsToIndexQuery($latestIndexedTimestamp);

        $query->searchable();
    }

    private function getLatestIndexedTimestamp(string $indexName)
    {
        $url = sprintf(
            '%s/indexes/%s/search',
            config('scout.meilisearch.host'),
            $indexName
        );

        $response = Http::withHeaders([
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer '.config('scout.meilisearch.key'),
        ])->post($url, [
            'q'                    => '*',
            'limit'                => 1,
            'attributesToRetrieve' => ['timestamp'],
            'sort'                 => ['timestamp:desc'],
        ]);

        return $response->json('hits.0.timestamp');
    }
}
