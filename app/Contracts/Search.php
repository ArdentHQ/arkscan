<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Meilisearch\Contracts\SearchQuery;

interface Search
{
    public function search(string $query, int $limit): EloquentCollection;

    public static function mapMeilisearchResults(array $rawResults): Collection;

    public static function buildSearchQueryForIndex(string $query, int $limit): ?SearchQuery;
}
