<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Block;
use Illuminate\Database\Eloquent\Builder;

class IndexBlocks extends IndexModel
{
    public function handle(): void
    {
        $this->execute(indexName: 'blocks');
    }

    protected function elementsToIndexQuery(int $latestIndexedTimestamp): Builder
    {
        return Block::getSearchableQuery()->where('timestamp', '>', $latestIndexedTimestamp);
    }
}
