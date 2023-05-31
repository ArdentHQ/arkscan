<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Laravel\Scout\Builder;

interface Search
{
    public function search(string $query, int $limit): Builder | EloquentBuilder | null;
}
