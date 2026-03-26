<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Wallet;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Meilisearch\Contracts\SearchQuery;

final class WalletSearch implements Search
{
    use ValidatesTerm;

    /**
     * @return EloquentCollection<Wallet>
     */
    public function search(string $query, int $limit): EloquentCollection
    {
        if ($this->couldBeAddress($query)) {
            return Wallet::where('address', 'ilike', $query)->limit(1)->get();
        }

        if ($this->couldntBeAddress($query)) {
            /**
             * @var EloquentCollection<Wallet>
             */
            return (new Wallet())->newCollection([]);
        }

        $builder = Wallet::where('address', 'ilike', sprintf('%%%s%%', $query));

        if ($this->couldBeUsername($query)) {
            $quoted = substr(DB::connection('explorer')->getPdo()->quote($query), 1, -1);

            $builder->orWhereRaw(
                'lower(attributes::text)::jsonb @> lower(\'{"username":"'.$quoted.'"}\')::jsonb'
            );
        }

        return $builder->limit($limit)->get();
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
            ->setLimit(max($limit, 0));
    }
}
