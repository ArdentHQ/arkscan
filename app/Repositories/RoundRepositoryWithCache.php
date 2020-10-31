<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RoundRepository;
use App\Models\Round;
use Illuminate\Support\Collection;

final class RoundRepositoryWithCache implements RoundRepository
{
    use Concerns\ManagesCache;

    private RoundRepository $rounds;

    public function __construct(RoundRepository $rounds)
    {
        $this->rounds = $rounds;
    }

    public function allByRound(int $round): Collection
    {
        return $this->remember(fn () => $this->rounds->allByRound($round));
    }

    public function currentRound(): Round
    {
        return $this->remember(fn () => $this->rounds->currentRound());
    }
}
