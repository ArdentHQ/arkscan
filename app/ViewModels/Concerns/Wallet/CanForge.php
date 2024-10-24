<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Actions\CacheNetworkHeight;
use App\Facades\Rounds;
use App\Services\BigNumber;
use App\Services\Cache\RequestScopedCache;
use App\Services\Cache\ValidatorCache;
use App\Services\Cache\WalletCache;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Illuminate\Support\Arr;

trait CanForge
{
    public function totalForged(): float
    {
        return $this->feesForged()->plus($this->rewardsForged()->valueOf())->toFloat();
    }

    public function amountForged(): BigNumber
    {
        return BigNumber::new(Arr::get((new ValidatorCache())->getTotalAmounts(), $this->wallet->address, 0));
    }

    public function feesForged(): BigNumber
    {
        return BigNumber::new(Arr::get((new ValidatorCache())->getTotalFees(), $this->wallet->address, 0));
    }

    public function rewardsForged(): BigNumber
    {
        return BigNumber::new(Arr::get((new ValidatorCache())->getTotalRewards(), $this->wallet->address, 0));
    }

    public function productivity(): float
    {
        if (! $this->isValidator()) {
            return 0;
        }

        $address = $this->address();

        $productivity = (new WalletCache())->getProductivity($address);
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

        $address = $this->address();

        $performance = (new WalletCache())->getPerformance($address);

        $currentRound = $this->currentSlot();
        if ($currentRound['status'] === 'done') {
            $performance = [
                collect($performance)->last(),
                $currentRound['block'] !== null,
            ];
        }

        return $performance;
    }

    public function hasForged(): bool
    {
        $performance = collect($this->performance())
            ->filter(fn ($data) => ! is_null($data));

        return $performance->last() ?? false;
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
        return Arr::get($this->wallet->attributes, 'validatorProducedBlocks', 0);
    }

    public function missedBlocks(): int
    {
        if ($this->wallet->missed_blocks !== null) {
            return $this->wallet->missed_blocks;
        }

        $address = $this->address();

        return (new WalletCache())->getMissedBlocks($address);
    }

    public function blocksSinceLastForged(): ?int
    {
        $lastBlock = $this->lastBlock();
        if ($lastBlock === null) {
            return null;
        }

        $height = CacheNetworkHeight::execute();

        return $height - $lastBlock['height'];
    }

    public function durationSinceLastForged(): ?string
    {
        $lastBlock = $this->lastBlock();
        if ($lastBlock === null) {
            return null;
        }

        $difference = CarbonInterval::instance(Carbon::parse($lastBlock['timestamp'])->diff());
        $difference->ceilMinutes();
        if ($difference->totalHours < 1) {
            return trans('general.time.minutes_short', ['minutes' => $difference->minutes]);
        }

        if ($difference->totalHours >= 1 && $difference->totalDays < 1) {
            if ($difference->minutes === 0) {
                return trans('general.time.hours_short', ['hours' => $difference->hours]);
            }

            return trans('general.time.hours_minutes_short', [
                'hours'   => $difference->hours,
                'minutes' => $difference->minutes,
            ]);
        }

        return trans('general.time.more_than_a_day');
    }

    public function currentSlot(): array
    {
        $validators = RequestScopedCache::remember('wallet:validators', function () {
            return Rounds::validators();
        });

        return $validators->firstWhere('address', $this->address());
    }

    private function lastBlock(): ?array
    {
        $address = $this->address();

        $lastBlock = (new WalletCache())->getLastBlock($address);
        if ($lastBlock === []) {
            return null;
        }

        return $lastBlock;
    }
}
