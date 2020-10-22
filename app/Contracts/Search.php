<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Search
{
    public function search(array $parameters): Builder;
}
