<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Services\Cache\DelegateCache;
use App\Services\Cache\WalletCache;
use Illuminate\Support\Arr;

trait CanForge
{
    public function totalForged(): float
    {
        return ($this->feesForged() + $this->rewardsForged()) / 1e8;
    }

    public function amountForged(): int
    {
        return (int) Arr::get((new DelegateCache())->getTotalAmounts(), $this->wallet->public_key, 0);
    }

    public function feesForged(): int
    {
        return (int) Arr::get((new DelegateCache())->getTotalFees(), $this->wallet->public_key, 0);
    }

    public function rewardsForged(): int
    {
        return (int) Arr::get((new DelegateCache())->getTotalRewards(), $this->wallet->public_key, 0);
    }

    public function blocksForged(): int
    {
        return (int) Arr::get((new DelegateCache())->getTotalBlocks(), $this->wallet->public_key, 0);
    }

    public function productivity(): float
    {
        if (! $this->isDelegate()) {
            return 0;
        }

        $publicKey = $this->publicKey();

        if (is_null($publicKey)) {
            return 0;
        }

        return (new WalletCache())->getProductivity($publicKey);
    }

    public function performance(): array
    {
        if (! $this->isDelegate()) {
            return [];
        }

        $publicKey = $this->publicKey();

        if (is_null($publicKey)) {
            return [];
        }

        return (new WalletCache())->getPerformance($publicKey);
    }

    public function hasForged(): bool
    {
        $performance = collect($this->performance());

        if ($performance->isNotEmpty()) {
            return $performance->last();
        }

        return false;
    }

    public function justMissed(): bool
    {
        // @TODO: check if we are past our slot

        return ! $this->hasForged();
    }

    public function keepsMissing(): bool
    {
        return array_slice(array_reverse($this->performance()), 0, 2) === [false, false];
    }
}
