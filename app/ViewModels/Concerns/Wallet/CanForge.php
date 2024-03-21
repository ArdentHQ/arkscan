<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Services\Cache\ValidatorCache;
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
        return (int) Arr::get((new ValidatorCache())->getTotalAmounts(), $this->wallet->public_key, 0);
    }

    public function feesForged(): int
    {
        return (int) Arr::get((new ValidatorCache())->getTotalFees(), $this->wallet->public_key, 0);
    }

    public function rewardsForged(): int
    {
        return (int) Arr::get((new ValidatorCache())->getTotalRewards(), $this->wallet->public_key, 0);
    }

    public function productivity(): float
    {
        if (! $this->isValidator()) {
            return 0;
        }

        $publicKey = $this->publicKey();

        if (is_null($publicKey)) {
            return 0;
        }

        $productivity = (new WalletCache())->getProductivity($publicKey);
        if ($productivity <= 0) {
            return 0;
        }

        return $productivity;
    }

    public function performance(): array
    {
        if (! $this->isValidator()) {
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
        $performance = collect($this->performance())
            ->filter(fn ($data) => ! is_null($data));

        return $performance->last() ?? false;
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

    public function forgedBlocks(): int
    {
        return Arr::get($this->wallet->attributes, 'validatorProducedBlocks', 0);
    }

    public function missedBlocks(): int
    {
        if ($this->wallet->missed_blocks !== null) {
            return $this->wallet->missed_blocks;
        }

        $publicKey = $this->publicKey();

        if (is_null($publicKey)) {
            return 0;
        }

        return (new WalletCache())->getMissedBlocks($publicKey);
    }
}
