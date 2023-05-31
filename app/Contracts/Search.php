<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Support\Collection;

interface Search
{
    public function search(string $query, int $limit): Collection;
}
