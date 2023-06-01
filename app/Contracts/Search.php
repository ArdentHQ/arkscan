<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

interface Search
{
    public function search(string $query, int $limit): EloquentCollection;

    public static function mapMeilisearchResults(array $rawResults): Collection;
}
