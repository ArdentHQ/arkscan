<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RoundRepository;
use App\Repositories\Concerns\ManagesCache;
use Illuminate\Cache\TaggedCache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

final class RoundRepositoryWithCache implements RoundRepository
{
    use ManagesCache;

    public function __construct(private RoundRepository $rounds)
    {
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
