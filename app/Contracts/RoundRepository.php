<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Round;
use Illuminate\Database\Eloquent\Collection;

interface RoundRepository
{
    public function current(): Round;

    public function byRound(int $round): Round;
}
