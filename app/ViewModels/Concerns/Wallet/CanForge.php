<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Facades\Rounds;
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

    public function productivity(): float
    {
        if (! $this->isDelegate()) {
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
        if (! $this->isDelegate()) {
            return [];
        }

        $publicKey = $this->publicKey();

        if (is_null($publicKey)) {
            return [];
        }

        $performance = (new WalletCache())->getPerformance($publicKey);

        $currentRound = $this->currentSlot();
        if ($currentRound['status'] === 'done') {
            $performance = [
                $performance[1],
                $currentRound['block'] !== null,
            ];
        }

        return $performance;
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
        return ! $this->hasForged();
    }

    public function keepsMissing(): bool
    {
        return $this->performance() === [false, false];
    }

    public function forgedBlocks(): int
    {
        return Arr::get($this->wallet->attributes, 'delegate.producedBlocks', 0);
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

    public function currentSlot(): array
    {
        return Rounds::delegates()->firstWhere('publicKey', $this->publicKey());
    }
}
