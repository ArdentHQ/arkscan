<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\RoundRepository as Contract;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Block;
use App\Models\Round;
use App\Services\Monitor\DelegateTracker;
use App\Services\Monitor\Monitor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

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
        return Round::max('round') ?: 1;
    }

    public function delegates(bool $withBlock = true): SupportCollection
    {
        $roundNumber = Rounds::current();
        $delegates   = Rounds::allByRound($roundNumber);
        $heightRange = Monitor::heightRangeByRound($roundNumber);
        $delegates   = new SupportCollection(DelegateTracker::execute($delegates, $heightRange[0]));
        // dump($delegates, $heightRange);
        if ($withBlock) {
            $blocks = Block::whereBetween('height', $heightRange)->get()->keyBy('generator_public_key');

            $delegates = $delegates->map(fn ($delegate) => [
                ...$delegate,

                'block' => $blocks->get($delegate['publicKey']),
            ]);
        }

        return $delegates;
    }
}
