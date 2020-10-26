<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Round;
use App\Models\Wallet;
use Illuminate\Support\Collection;

final class Monitor
{
    public function activeDelegates(): Collection
    {
        return Wallet::query()
            ->whereNotNull('delegate')
            ->where('attributes->delegate->resigned', false)
            ->orderBy('vote_balance', 'desc')
            ->limit(Network::delegateCount())
            ->get();
    }

    public function standbyDelegates(): Collection
    {
        return Wallet::query()
            ->whereNotNull('delegate')
            ->orderBy('vote_balance', 'desc')
            ->skip(Network::delegateCount())
            ->limit(Network::delegateCount())
            ->get();
    }

    public function resignedDelegates(): Collection
    {
        return Wallet::query()
            ->whereNotNull('delegate')
            ->where('attributes->delegate->resigned', true)
            ->orderBy('vote_balance', 'desc')
            ->get();
    }

    public function roundNumber(): int
    {
        return Round::number()->firstOrFail()->round;
    }

    public function lastFiveRounds(int $round): Collection
    {
        return Round::query()
            ->whereBetween('round', [$round - 4, $round])
            ->get()
            ->groupBy('round');
    }

    public function heightRange(int $round): Collection
    {
        return collect(range($round - 4, $round))->mapWithKeys(function ($round): array {
            $roundStart = (int) $round * Network::delegateCount();

            return [
                $round => [
                    'min' => $roundStart,
                    'max' => $roundStart + 50,
                ],
            ];
        });
    }

    public function status(string $publicKey): Collection
    {
        $round   = $this->roundNumber();
        $heights = $this->heightRange($round);

        return $heights->map(function ($round) use ($publicKey): bool {
            return Block::query()
                ->where('generator_public_key', $publicKey)
                ->whereBetween('height', [$round['min'], $round['max']])
                ->count() > 0;
        });
    }

    public function blocks(string $publicKey): Collection
    {
        return Block::query()
            ->latestByHeight()
            ->where('generator_public_key', $publicKey)
            ->limit(5)
            ->pluck('height');
    }

    public function lastForged(string $publicKey): int
    {
        return (int) ceil(Block::query()
            ->latestByHeight()
            ->where('generator_public_key', $publicKey)
            ->limit(1)
            ->pluck('height')[0] / Network::delegateCount());
    }
}
