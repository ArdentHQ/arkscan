<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Block;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;

final class BlockSearch implements Search
{
    use ValidatesTerm;

    /**
     * @return EloquentCollection<Block>
     */
    public function search(string $query, int $limit): EloquentCollection
    {
        if ($this->couldBeBlockID($query)) {
            $builder = Block::where('id', $query)->take(1);
        } else if ($this->couldBeHeightValue($query)) {
            $builder = Block::where('height', $query)->take(1);
        } else {
            $builder = Block::where('id', 'ilike', sprintf('%%%s%%', $query))->limit($limit);
        }

        return $builder->get();
    }

    public static function mapMeilisearchResults(array $rawResults): Collection
    {
        return collect($rawResults)->map(fn ($item) => new Block($item));
    }
}
