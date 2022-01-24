<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RoundRepository as Contract;
use App\Facades\Network;
use App\Models\Round;
use Illuminate\Database\Eloquent\Collection;

final class RoundRepository implements Contract
{
    public function allByRound(int $round): Collection
    {
        return Round::query()
            ->where('round', $round)
            ->orderBy('balance', 'desc')
            ->orderBy('public_key', 'asc')
            ->limit(Network::delegateCount())
            ->get();
    }

    public function current(): int
    {
        return Round::max('round');
    }
}
