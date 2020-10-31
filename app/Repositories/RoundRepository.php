<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RoundRepository as Contract;
use App\Models\Round;
use Illuminate\Support\Collection;

final class RoundRepository implements Contract
{
    public function allByRound(int $round): Collection
    {
        return Round::query()
            ->where('round', $round)
            ->orderBy('balance', 'desc')
            ->orderBy('public_key', 'asc')
            ->get();
    }

    public function currentRound(): Round
    {
        return Round::orderBy('round', 'desc')->firstOrFail();
    }
}
