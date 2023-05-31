<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Block;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Support\Collection;

final class BlockSearch implements Search
{
    use ValidatesTerm;

    public function search(string $query, int $limit): Collection
    {
        if ($this->couldBeBlockID($query) || $this->couldBeHeightValue($query)) {
            // Quoted so it gets the exact match
            $builder = Block::search(sprintf('"%s"', $query))->take(1);
        } else {
            $builder = Block::search($query)->take($limit);
        }

        return collect($builder->raw()['hits'])->map(fn ($item) => new Block($item));
    }
}
