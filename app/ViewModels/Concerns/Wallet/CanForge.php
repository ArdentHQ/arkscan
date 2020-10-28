<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Facades\Network;
use App\Services\BigNumber;
use App\Services\NumberFormatter;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

trait CanForge
{
    public function amountForged(): string
    {
        $result = Arr::get(Cache::get('delegates.totalAmounts', []), $this->wallet->public_key, 0);

        return NumberFormatter::currency(BigNumber::new($result)->toFloat(), Network::currency());
    }

    public function feesForged(): string
    {
        $result = Arr::get(Cache::get('delegates.totalFees', []), $this->wallet->public_key, 0);

        return NumberFormatter::currency(BigNumber::new($result)->toFloat(), Network::currency());
    }

    public function rewardsForged(): string
    {
        $result = Arr::get(Cache::get('delegates.totalRewards', []), $this->wallet->public_key, 0);

        return NumberFormatter::currency(BigNumber::new($result)->toFloat(), Network::currency());
    }

    public function blocksForged(): string
    {
        $result = Arr::get(Cache::get('delegates.totalBlocks', []), $this->wallet->public_key, 0);

        return NumberFormatter::number($result);
    }

    public function forgedBlocks(): string
    {
        return NumberFormatter::number(Arr::get($this->wallet, 'attributes.delegate.producedBlocks', 0));
    }

    /**
     * @TODO: needs monitor to be implemented
     */
    public function productivity(): string
    {
        return NumberFormatter::percentage(0);
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
