<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Block;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Laravel\Scout\Builder;

final class BlockSearch implements Search
{
    use ValidatesTerm;

    public function search(string $query, int $limit): Builder|EloquentBuilder
    {
        if ($this->couldBeBlockID($query) || $this->couldBeHeightValue($query)) {
            // We can use a regular query since is a exact match
            return Block::where('id', strtolower($query))->limit(1);
        }

        return Block::search($query)->take($limit);
    }
}
