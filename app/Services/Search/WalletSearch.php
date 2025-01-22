<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Wallet;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Meilisearch\Contracts\SearchQuery;

final class WalletSearch implements Search
{
    use ValidatesTerm;

    /**
     * @return EloquentCollection<Wallet>
     */
    public function search(string $query, int $limit): EloquentCollection
    {
        if ($this->couldntBeAddress($query)) {
            /**
             * @var EloquentCollection<Wallet>
             */
            return (new Wallet())->newCollection([]);
        }

        if ($this->couldBeAddress($query)) {
            $builder = Wallet::where('address', 'ilike', $query)->limit(1);
        } else {
            $builder = Wallet::where('address', 'ilike', sprintf('%%%s%%', $query))->limit($limit);
        }

        return $builder->get();
    }

    public static function mapMeilisearchResults(array $rawResults): Collection
    {
        return collect($rawResults)->map(fn ($item) => new Wallet([
            ...$item,
            'attributes' => [
                'username' => Arr::get($item, 'username'),
            ],
        ]));
    }

    public static function buildSearchQueryForIndex(string $query, int $limit): ?SearchQuery
    {
        if ((new self())->couldntBeAddress($query)) {
            return null;
        }

        if ((new self())->couldBeAddress($query)) {
            $query = sprintf('"%s"', $query);
        }

        return (new SearchQuery())
            ->setQuery($query)
            ->setIndexUid('wallets')
            ->setLimit($limit);
    }
}
