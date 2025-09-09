<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators\Concerns;

use App\DTO\Slot;
use App\Enums\ValidatorForgingStatus;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\MonitorCache;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\Monitor;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;

trait HandlesMonitorDataBoxes
{
    private array $statistics = [];

    public function pollStatistics(): void
    {
        $this->statistics = [
            'blockCount'    => $this->getBlockCount(),
            'nextValidator' => $this->getNextValidator(),
            'performances'  => $this->getValidatorsPerformance(),
        ];
    }

    public function getValidatorsPerformance(): array
    {
        $performances = [];

        foreach ($this->validators as $validator) {
            $address                = $validator->wallet()->model()->address;
            $performances[$address] = $this->getValidatorPerformance($address);
        }

        $parsedPerformances = array_count_values($performances);

        return [
            'forging' => $parsedPerformances[ValidatorForgingStatus::forging] ?? 0,
            'missed'  => $parsedPerformances[ValidatorForgingStatus::missed] ?? 0,
            'missing' => $parsedPerformances[ValidatorForgingStatus::missing] ?? 0,
        ];
    }

    public function getValidatorPerformance(string $address): string
    {
        /** @var Wallet $validatorWallet */
        $validatorWallet = (new WalletCache())->getValidator($address);

        /** @var WalletViewModel $validator */
        $validator = ViewModelFactory::make($validatorWallet);

        if ($validator->hasForged()) {
            return ValidatorForgingStatus::forging;
        } elseif ($validator->keepsMissing()) {
            return ValidatorForgingStatus::missing;
        }

        // NOTE: In the first round of a newly registered validator it is always considered "missed"
        // because we don't have the information here to know whether the validator just joined or not.
        // It will auto-correct itself after the first round.

        return ValidatorForgingStatus::missed;
    }

    public function getBlockCount(): string
    {
        return (new MonitorCache())->setBlockCount(function (): string {
            return trans('pages.validators.statistics.blocks_generated', [
                'forged' => Network::validatorCount() - (Monitor::heightRangeByRound(Rounds::current())[1] - Block::max('number')),
                'total'  => Network::validatorCount(),
            ]);
        });
    }

    public function getNextValidator(): ? WalletViewModel
    {
        $validators = [
            ...$this->validators,
            ...$this->overflowValidators,
        ];

        return (new MonitorCache())->setNextValidator(fn () => optional($this->getSlotsByStatus($validators, 'pending'))->wallet());
    }

    private function getSlotsByStatus(array $slots, string $status): ?Slot
    {
        return collect($slots)
            ->filter(fn ($slot) => $slot->status() === $status)
            ->first();
    }
}
