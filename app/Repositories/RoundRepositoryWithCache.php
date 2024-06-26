<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RoundRepository;
use App\Facades\Network;
use App\Repositories\Concerns\ManagesCache;
use Illuminate\Cache\TaggedCache;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
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

    public function delegates(bool $withBlock = true): SupportCollection
    {
        return $this->remember(fn () => $this->rounds->delegates($withBlock), Network::blockTime() / 2);
    }

    private function getCache(): TaggedCache
    {
        return Cache::tags('rounds');
    }
}
