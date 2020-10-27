<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;
use App\Models\Round;
use Illuminate\Support\Collection;

final class Monitor
{
    public static function activeDelegates(int $round): Collection
    {
        return Round::query()
           ->where('round', $round)
           ->orderBy('balance', 'desc')
           ->orderBy('public_key', 'asc')
           ->get();
    }

    public static function roundNumber(): int
    {
        return Round::number()->firstOrFail()->round;
    }

    public static function lastFiveRounds(int $round): Collection
    {
        return Round::query()
            ->whereBetween('round', [$round - 4, $round])
            ->get()
            ->groupBy('round');
    }

    public static function heightRange(int $round): Collection
    {
        return collect(range($round - 6, $round - 2))->mapWithKeys(function ($round): array {
            $roundStart = (int) $round * Network::delegateCount();

            return [
                $round => [
                    'min' => $roundStart,
                    'max' => $roundStart + 50,
                ],
            ];
        });
    }

    public static function heightRangeByRound(int $round): array
    {
        $roundStart = (int) $round * Network::delegateCount();

        return [$roundStart, $roundStart + 50];
    }

    public static function status(string $publicKey): Collection
    {
        $round   = static::roundNumber();
        $heights = static::heightRange($round);

        return $heights->map(function ($round) use ($publicKey): bool {
            return Block::query()
                ->where('generator_public_key', $publicKey)
                ->whereBetween('height', [$round['min'], $round['max']])
                ->count() > 0;
        });
    }

    public static function blocks(string $publicKey): Collection
    {
        return Block::query()
            ->latestByHeight()
            ->where('generator_public_key', $publicKey)
            ->limit(5)
            ->pluck('height');
    }

    public static function lastForged(string $publicKey): int
    {
        return (int) ceil(Block::query()
            ->latestByHeight()
            ->where('generator_public_key', $publicKey)
            ->limit(1)
            ->pluck('height')[0] / Network::delegateCount());
    }
}
