<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

trait CanForge
{
    public function totalForged(): float
    {
        $fees    = (int) Arr::get(Cache::get('delegates.totalFees', []), $this->wallet->public_key, 0);
        $rewards = (int) Arr::get(Cache::get('delegates.totalRewards', []), $this->wallet->public_key, 0);

        return ($fees + $rewards) / 1e8;
    }

    public function amountForged(): int
    {
        return (int) Arr::get(Cache::get('delegates.totalAmounts', []), $this->wallet->public_key, 0);
    }

    public function feesForged(): int
    {
        return (int) Arr::get(Cache::get('delegates.totalFees', []), $this->wallet->public_key, 0);
    }

    public function rewardsForged(): int
    {
        return (int) Arr::get(Cache::get('delegates.totalRewards', []), $this->wallet->public_key, 0);
    }

    public function blocksForged(): int
    {
        return (int) Arr::get(Cache::get('delegates.totalBlocks', []), $this->wallet->public_key, 0);
    }

    public function forgedBlocks(): int
    {
        return (int) Arr::get($this->wallet, 'attributes.delegate.producedBlocks', 0);
    }

    /**
     * @TODO: needs monitor to be implemented
     */
    public function productivity(): int
    {
        return 0;
    }

    public function performance(): array
    {
        if (! $this->isDelegate()) {
            return [];
        }

        return Cache::get('performance:'.$this->publicKey(), []);
    }

    public function justMissed(): bool
    {
        $missedOne  = collect($this->performance())->filter(fn ($performance) => $performance === false)->count() === 1;
        $missedLast = collect($this->performance())->last() === false;

        return $missedOne && $missedLast;
    }

    public function isMissing(): bool
    {
        return collect($this->performance())
            ->filter(fn ($performance) => $performance === false)
            ->count() > 1;
    }
}
