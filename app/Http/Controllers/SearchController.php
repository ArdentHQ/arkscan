<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\ViewModel;
use App\Facades\Network;
use App\DTO\Search\NavbarSearchBlockResultData;
use App\DTO\Search\NavbarSearchTransactionResultData;
use App\DTO\Search\NavbarSearchWalletResultData;
use App\Services\Search\BlockSearch;
use App\Services\Search\TransactionSearch;
use App\Services\Search\WalletSearch;
use App\ViewModels\BlockViewModel;
use App\ViewModels\TransactionViewModel;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Laravel\Scout\Engines\MeilisearchEngine;
use Meilisearch\Contracts\SearchQuery;

const RESULT_LIMIT_PER_TYPE = 5;

final class SearchController
{
    public ?string $query = null;

    public function index(Request $request): JsonResponse
    {
        $this->query = $request->input('query');

        $results = $this->results();

        return response()->json([
            'results'    => $results
            ->map(fn (ViewModel $result) => $this->serializeResult($result))
            ->toArray(),
            'hasResults' => $results->isNotEmpty(),
        ]);
    }

    private function serializeResult(ViewModel $result): array
    {
        /**
         * @var WalletViewModel|BlockViewModel|TransactionViewModel $result
         */
        return [
            'type'       => $this->determineType($result),
            'url'        => $result->url(),
            'identifier' => method_exists($result, 'id') ? $result->id() : (method_exists($result, 'hash') ? $result->hash() : null),
            'data'       => $this->toArray($result),
        ];
    }

    private function determineType(ViewModel $result): string
    {
        return match (true) {
            $result instanceof WalletViewModel      => 'wallet',
            $result instanceof BlockViewModel       => 'block',
            $result instanceof TransactionViewModel => 'transaction',
            default                                 => throw new \Exception('Invalid result type: '.get_class($result)),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function toArray(ViewModel $result): array
    {
        if ($result instanceof WalletViewModel) {
            return NavbarSearchWalletResultData::fromViewModel($result)->toArray();
        }

        if ($result instanceof BlockViewModel) {
            return NavbarSearchBlockResultData::fromViewModel($result)->toArray();
        }

        if ($result instanceof TransactionViewModel) {
            return NavbarSearchTransactionResultData::fromViewModel($result)->toArray();
        }

        // @codeCoverageIgnoreStart
        throw new \Exception('Invalid result type: '.get_class($result));
        // @codeCoverageIgnoreEnd
    }

    public function results(): Collection
    {
        $validator = Validator::make([
            'query' => $this->query,
        ], [
            'query' => [
                'required', 'string', 'max:66',
            ],
        ]);

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

        return ViewModelFactory::collection($results);
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
}
