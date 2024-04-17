<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RoundRepository;
use App\Facades\Network;
use App\Models\Round;
use App\Repositories\Concerns\ManagesCache;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\Cache;

final class RoundRepositoryWithCache implements RoundRepository
{
    use ManagesCache;

    public function __construct(private RoundRepository $rounds)
    {
    }

    public function current(): Round
    {
        return $this->rounds->current();
    }

    public function byRound(int $round): Round
    {
        return $this->remember(fn () => $this->rounds->byRound($round));
    }

    public function validators(bool $withBlock = true): SupportCollection
    {
        return $this->remember(fn () => $this->rounds->validators($withBlock), Network::blockTime() / 2);
    }

    private function getCache(): TaggedCache
    {
        return Cache::tags('rounds');
    }
}
