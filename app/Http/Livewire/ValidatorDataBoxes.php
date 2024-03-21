<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Actions\CacheNetworkHeight;
use App\DTO\Slot;
use App\Enums\ValidatorForgingStatus;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\ValidatorData;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\MonitorCache;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\Monitor;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\View\View;
use Livewire\Component;

final class ValidatorDataBoxes extends Component
{
    use DeferLoading;
    use ValidatorData;

    private array $validators = [];

    private array $statistics = [];

    public function render(): View
    {
        $this->validators = $this->fetchValidators();

        return view('livewire.validator-data-boxes', [
            'height'     => CacheNetworkHeight::execute(),
            'statistics' => $this->statistics,
        ]);
    }

    public function pollStatistics(): void
    {
        if (! $this->isReady) {
            return;
        }

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
            $publicKey                = $validator->wallet()->model()->public_key;
            $performances[$publicKey] = $this->getValidatorPerformance($publicKey);
        }

        $parsedPerformances = array_count_values($performances);

        return [
            'forging' => $parsedPerformances[ValidatorForgingStatus::forging] ?? 0,
            'missed'  => $parsedPerformances[ValidatorForgingStatus::missed] ?? 0,
            'missing' => $parsedPerformances[ValidatorForgingStatus::missing] ?? 0,
        ];
    }

    public function getValidatorPerformance(string $publicKey): string
    {
        /** @var Wallet $validatorWallet */
        $validatorWallet = (new WalletCache())->getValidator($publicKey);

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
                'forged' => Network::validatorCount() - (Monitor::heightRangeByRound(Rounds::current())[1] - Block::max('height')),
                'total'  => Network::validatorCount(),
            ]);
        });
    }

    public function getNextValidator(): ? WalletViewModel
    {
        $this->validators = $this->fetchValidators();

        return (new MonitorCache())->setNextValidator(fn () => optional($this->getSlotsByStatus($this->validators, 'pending'))->wallet());
    }

    private function getSlotsByStatus(array $slots, string $status): ?Slot
    {
        return collect($slots)
            ->filter(fn ($slot) => $slot->status() === $status)
            ->first();
    }
}
