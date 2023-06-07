<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

abstract class IndexModel implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    abstract public function uniqueId(): string;

    /**
     * Execute the job.
     * @return void
     */
    abstract public function handle(): void;

    abstract protected function elementsToIndexQuery(int $latestIndexedTimestamp): Builder;

    final protected function execute(string $indexName): void
    {
        $latestIndexedTimestamp = $this->getLatestIndexedTimestamp($indexName);

        // If there are no indexed transactions yet we should not index anything
        // and wait for the user to run `php artisan scout:import "App\Models\Transaction"`
        // for the first time
        if ($latestIndexedTimestamp === null) {
            return;
        }

        $query = $this->elementsToIndexQuery($latestIndexedTimestamp);

        // @phpstan-ignore-next-line
        $query->searchable();

        if ($query->count() === 0) {
            return;
        }

        $latestTimestamp = $query->orderBy('timestamp', 'desc')->first()->timestamp;

        $this->updateLatestIndexedTimestamp($indexName, $latestTimestamp);
    }

    protected function getLatestIndexedTimestamp(string $indexName): int|null
    {
        $timestamp = $this->getLatestIndexedTimestampFromCache($indexName);

        if ($timestamp !== null) {
            return $timestamp;
        }

        return $this->getLatestIndexedTimestampFromMeilisearch($indexName);
    }

    private function getCacheKey(string $indexName): string
    {
        return sprintf('latest-indexed-timestamp:%s', $indexName);
    }

    private function updateLatestIndexedTimestamp(string $indexName, int $latestTimestamp): void
    {
        Cache::put($this->getCacheKey($indexName), $latestTimestamp);
    }

    private function getLatestIndexedTimestampFromCache(string $indexName): int|null
    {
        $timestamp = Cache::get($this->getCacheKey($indexName));

        if ($timestamp === null) {
            return null;
        }

        return (int) $timestamp;
    }

    private function getLatestIndexedTimestampFromMeilisearch(string $indexName): int|null
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
