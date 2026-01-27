<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\Contracts\ViewModel;
use App\DTO\Inertia\Transaction as TransactionDTO;
use App\Facades\Network;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\Search\BlockSearch;
use App\Services\Search\TransactionSearch;
use App\Services\Search\WalletSearch;
use App\ViewModels\BlockViewModel;
use App\ViewModels\WalletViewModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;
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

        return $this->mapResults($results);
    }

    /**
     * Uses Meilisearch multisearch capabilities to search across multiple indexes.
     */
    public function searchWithMeilisearch(string $query): Collection
    {
        $indexUids = collect(['wallets', 'transactions', 'blocks']);

        $searchQueries = $indexUids
                ->map(fn ($indexUid) => $this->buildSearchQueryForIndex($query, $indexUid))
                ->filter(fn ($query) => $query !== null);

        $knownWalletsAddresses = $this->matchKnownWalletsAddresses($query);

        if ($knownWalletsAddresses->count() > 0) {
            $knownWalletsAddresses->each(function ($address) use ($searchQueries) {
                /**
                 * @var SearchQuery
                 */
                $query = $this->buildSearchQueryForIndex($address, 'wallets');
                $searchQueries->push($query);
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

        return $this->mapResults($results);
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
            ->map(fn ($wallet) => $wallet['address'])
            ->take(RESULT_LIMIT_PER_TYPE);
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

    private function mapResults(Collection $results): Collection
    {
        return $results->map(function ($result) {
            return match (true) {
                $result instanceof Wallet      => new WalletViewModel($result),
                $result instanceof Transaction => TransactionDTO::fromModel($result),
                $result instanceof Block       => new BlockViewModel($result),
                $result instanceof ViewModel   => $result,
                default                        => throw new InvalidArgumentException('Invalid search result type.'),
            };
        });
    }
}
