<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Block;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Meilisearch\Contracts\SearchQuery;

final class BlockSearch implements Search
{
    use ValidatesTerm;

    /**
     * @return EloquentCollection<Block>
     */
    public function search(string $query, int $limit): EloquentCollection
    {
        if ($this->couldBeBlockID($query)) {
            $builder = Block::where('id', strtolower($query))->take(1);
        } else {
            $builder = Block::where('id', 'ilike', sprintf('%%%s%%', $query))->limit($limit);
        }

        return $builder->get();
    }

    public static function mapMeilisearchResults(array $rawResults): Collection
    {
        return collect($rawResults)->map(fn ($item) => new Block($item));
    }

    public static function buildSearchQueryForIndex(string $query, int $limit): SearchQuery
    {
        if ((new self)->couldBeAddress($query)) {
            return null;
        }

        if ((new self)->couldBeBlockID($query)) {
            $query = sprintf('"%s"', $query);
        }

        return (new SearchQuery())
            ->setQuery($query)
            ->setIndexUid('blocks')
            ->setLimit($limit);
    }
}
