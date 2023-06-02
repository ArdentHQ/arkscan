<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Facades\Network;
use App\Services\Search\BlockSearch;
use App\Services\Search\TransactionSearch;
use App\Services\Search\WalletSearch;
use App\ViewModels\ViewModelFactory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Laravel\Scout\Engines\MeilisearchEngine;
use Meilisearch\Contracts\SearchQuery;

const RESULT_LIMIT_PER_TYPE = 5;

trait ManagesSearch
{
    public ?string $query = null;

    protected array $rules = [
        'query' => [
            'required', 'string', 'max:66',
        ],
    ];

    public function clear(): void
    {
        $this->query = null;
    }

    public function results(): Collection
    {
        $validator = Validator::make([
            'query' => $this->query,
        ], $this->rules);

        if ($validator->fails()) {
            return new Collection();
        }

        $data = $validator->validate();

        $query = $this->parseQuery(Arr::get($data, 'query'));

        if (config('scout.driver') === 'meilisearch') {
            return $this->searchWithMeilisearch($query);
        }

        $results = (new WalletSearch())->search(query: $query, limit: RESULT_LIMIT_PER_TYPE);
        $results = $results->concat((new TransactionSearch())->search(query: $query, limit: RESULT_LIMIT_PER_TYPE));
        $results = $results->concat((new BlockSearch())->search(query: $query, limit: RESULT_LIMIT_PER_TYPE));

        return ViewModelFactory::collection($results);
    }

    /**
     * Uses Meilisearch multisearch capabilities to search across multiple indexes.
     */
    public function searchWithMeilisearch(string $query): Collection
    {
        $indexUids = collect(['wallets', 'transactions', 'blocks']);

        $searchQueries = $indexUids
                ->map(fn ($indexUid) => $this->buildSearchQueryForIndex($query, $indexUid))
                ->filter();

        $knownWalletsAddresses = $this->matchKnownWalletsAddresses($query);

        if ($knownWalletsAddresses->count() > 0) {
            $knownWalletsAddresses->each(function ($address) use ($searchQueries) {
                $searchQueries->push($this->buildSearchQueryForIndex($address, 'wallets'));
            });
        }

        $response = app(MeilisearchEngine::class)->__call('multiSearch', [
            $searchQueries->toArray(),
        ]);

        /**
         * @var array<int, mixed>
         */
        $results = Arr::get($response, 'results');

        $results =  collect($results)
            ->flatMap(function ($result) {
                $indexUid = $result['indexUid'];
                $hits     = $result['hits'];

                if ($indexUid === 'wallets') {
                    return WalletSearch::mapMeilisearchResults($hits);
                }

                if ($indexUid === 'transactions') {
                    return TransactionSearch::mapMeilisearchResults($hits);
                }

                if ($indexUid === 'blocks') {
                    return BlockSearch::mapMeilisearchResults($hits);
                }
            });

        return ViewModelFactory::collection($results);
    }

    public function goToFirstResult(): null|Redirector|RedirectResponse
    {
        $results = $this->results();

        if ($results->isEmpty()) {
            return null;
        }

        return redirect($results->first()->url());
    }

    private function parseQuery(string $query): string
    {
        // Remove all special characters from the beginning and end of the query.
        $chars = implode('', ['*', '"', '\'', ' ', '.']);

        return ltrim(rtrim($query, $chars), $chars);
    }

    private function matchKnownWalletsAddresses(string $query): Collection
    {
        $knownWallets = collect(Network::knownWallets());

        return $knownWallets
            ->filter(fn ($wallet) => str_contains(strtolower($wallet['name']), strtolower($query)))
            ->map(fn ($wallet) => $wallet['address']);
    }

    private function buildSearchQueryForIndex(string $query, string $indexUid): ?SearchQuery
    {
        if ($indexUid === 'transactions') {
            return TransactionSearch::buildSearchQueryForIndex($query, RESULT_LIMIT_PER_TYPE);
        }

        if ($indexUid === 'blocks') {
            return BlockSearch::buildSearchQueryForIndex($query, RESULT_LIMIT_PER_TYPE);
        }

        return WalletSearch::buildSearchQueryForIndex($query, RESULT_LIMIT_PER_TYPE);
    }
}
