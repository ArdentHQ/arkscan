<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Facades\Network;
use App\Models\Block;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

final class CachePastRoundPerformanceByPublicKey implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $round;

    public string $publicKey;

    public function __construct(int $round, string $publicKey)
    {
        $this->round     = $round;
        $this->publicKey = $publicKey;
    }

    public function handle(): void
    {
        $result = collect(range($this->round - 6, $this->round - 2))->mapWithKeys(function ($round): array {
            $roundStart = (int) $round * Network::delegateCount();

            return [
                $round => [
                    'min' => $roundStart,
                    'max' => $roundStart + (Network::delegateCount() - 1),
                ],
            ];
        })->map(function ($round): bool {
            return Block::query()
                ->where('generator_public_key', $this->publicKey)
                ->whereBetween('height', [$round['min'], $round['max']])
                ->count() > 0;
        });

        Cache::put('performance:'.$this->publicKey, $result->values()->toArray());
    }
}
