<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RoundRepository;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

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

    public function current(): int
    {
        return $this->rounds->current();
    }

    private function getCache(): TaggedCache
    {
        return Cache::tags('rounds');
    }
}
