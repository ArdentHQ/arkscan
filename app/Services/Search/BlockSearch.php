<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Block;
use App\Services\Search\Traits\ValidatesTerm;
use Laravel\Scout\Builder;

final class BlockSearch implements Search
{
    use ValidatesTerm;

    public function search(string $query): Builder
    {
        if ($this->couldBeBlockID($query) || $this->couldBeHeightValue($query)) {
            // Exact match
            return Block::search(sprintf('"%s"', $query));
        } else {
            return Block::search($query);
        }
    }
}
