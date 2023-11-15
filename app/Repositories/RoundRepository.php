<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RoundRepository as Contract;
use App\Models\Round;

final class RoundRepository implements Contract
{
     public function current(): Round
    {
        return Round::orderBy('round', 'desc')->firstOrFail();
    }

    public function byRound(int $round): Round
    {
        return Round::findOrFail($round);
    }
}
