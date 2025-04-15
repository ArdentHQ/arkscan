<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Transaction;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Meilisearch\Contracts\SearchQuery;

final class TransactionSearch implements Search
{
    use ValidatesTerm;

    /**
     * @return EloquentCollection<Transaction>
     */
    public function search(string $query, int $limit): EloquentCollection
    {
        if ($this->couldBeTransactionHash($query)) {
            $builder = Transaction::where('id', strtolower($query))->take(1);
        } else {
            $builder = Transaction::where('id', 'ilike', sprintf('%%%s%%', $query))->limit($limit);
        }

        return $builder->get();
    }

    public static function mapMeilisearchResults(array $rawResults): Collection
    {
        return collect($rawResults)->map(fn ($item) => new Transaction($item));
    }

    public static function buildSearchQueryForIndex(string $query, int $limit): ?SearchQuery
    {
        if ((new self())->couldBeAddress($query)) {
            return null;
        }

        return (new SearchQuery())
            ->setFilter(['id = '.sprintf('"%s"', addslashes($query))])
            ->setIndexUid('transactions')
            ->setLimit($limit);
    }
}
