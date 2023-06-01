<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Models\Wallet;
use App\Services\Search\BlockSearch;
use App\Services\Search\TransactionSearch;
use App\Services\Search\WalletSearch;
use App\ViewModels\ViewModelFactory;
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

        $query = Arr::get($data, 'query');

        if(config('scout.driver') === 'meilisearch') {

            return $this->searchWithMeilisearch($query);
        }

        $results = (new WalletSearch())->search(query: $query, limit: 5);
        $results = $results->concat((new TransactionSearch())->search(query: $query, limit: 5));

        // dd($results);;
        // $results = $results->concat((new BlockSearch())->search(query: $query, limit: 5));

        return ViewModelFactory::collection($results);
    }

    /**
     * Uses Meilisearch multisearch capabilities to search across multiple indexes.
     */
    public function searchWithMeilisearch(string $query): Collection
    {
        $indexUids = collect(['wallets', 'transactions', 'blocks']);

        $response = app(MeilisearchEngine::class)->__call("multiSearch", [
            $indexUids
                ->map(fn ($indexUid) => $this->buildSearchQueryForIndex($query, $indexUid))
                ->toArray()
        ]);

        $results =  collect(Arr::get($response, 'results'))
            ->mapWithKeys(fn ($result) => [$result['indexUid'] => $result['hits']])
            ->flatMap(function ($hits, $indexUid) {
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

    private function buildSearchQueryForIndex(string $query, string $indexUid): SearchQuery
    {
        return (new SearchQuery())
            ->setQuery($query)
            ->setIndexUid($indexUid)
            ->setLimit(RESULT_LIMIT_PER_TYPE);
    }
}
