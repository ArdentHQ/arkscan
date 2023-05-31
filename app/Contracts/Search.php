<?php

declare(strict_types=1);

namespace App\Contracts;

use Laravel\Scout\Builder;

interface Search
{
    public function search(string $query): Builder;
}
