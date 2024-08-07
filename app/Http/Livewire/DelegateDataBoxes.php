<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Actions\CacheNetworkHeight;
use App\DTO\Slot;
use App\Enums\DelegateForgingStatus;
use App\Facades\Network;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\DelegateData;
use App\Models\Block;
use App\Models\Wallet;
use App\Services\Cache\MonitorCache;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\Monitor;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\View\View;
use Livewire\Component;

final class DelegateDataBoxes extends Component
{
    use DeferLoading;
    use DelegateData;

    /** @var mixed */
    protected $listeners = [
        'echo:blocks,NewBlock' => 'pollStatistics',
    ];

    private array $delegates = [];

    private array $statistics = [];

    public function render(): View
    {
        $this->delegates = $this->fetchDelegates();

        return view('livewire.delegate-data-boxes', [
            'height'     => CacheNetworkHeight::execute(),
            'statistics' => $this->statistics,
        ]);
    }

    public function componentIsReady(): void
    {
        $this->setIsReady();

        $this->pollStatistics();
    }

    public function pollStatistics(): void
    {
        if (! $this->isReady) {
            return;
        }

        $this->statistics = [
            'blockCount'   => $this->getBlockCount(),
            'nextDelegate' => $this->getNextDelegate(),
            'performances' => $this->getDelegatesPerformance(),
        ];
    }

    public function getDelegatesPerformance(): array
    {
        $performances = [];

        foreach ($this->delegates as $delegate) {
            $publicKey                = $delegate->wallet()->model()->public_key;
            $performances[$publicKey] = $this->getDelegatePerformance($publicKey);
        }

        $parsedPerformances = array_count_values($performances);

        return [
            'forging' => $parsedPerformances[DelegateForgingStatus::forging] ?? 0,
            'missed'  => $parsedPerformances[DelegateForgingStatus::missed] ?? 0,
            'missing' => $parsedPerformances[DelegateForgingStatus::missing] ?? 0,
        ];
    }

    public function getDelegatePerformance(string $publicKey): string
    {
        /** @var Wallet $delegateWallet */
        $delegateWallet = (new WalletCache())->getDelegate($publicKey);

        /** @var WalletViewModel $delegate */
        $delegate = ViewModelFactory::make($delegateWallet);

        if ($delegate->hasForged()) {
            return DelegateForgingStatus::forging;
        } elseif ($delegate->keepsMissing()) {
            return DelegateForgingStatus::missing;
        }

        return DelegateForgingStatus::missed;
    }

    public function getBlockCount(): string
    {
        return (new MonitorCache())->setBlockCount(function (): string {
            return trans('pages.delegates.statistics.blocks_generated', [
                'forged' => Network::delegateCount() - (Monitor::heightRangeByRound(Monitor::roundNumber())[1] - Block::max('height')),
                'total'  => Network::delegateCount(),
            ]);
        });
    }

    public function getNextDelegate(): ? WalletViewModel
    {
        $this->delegates = $this->fetchDelegates();

        return (new MonitorCache())->setNextDelegate(fn () => optional($this->getSlotsByStatus($this->delegates, 'pending'))->wallet());
    }

    private function getSlotsByStatus(array $slots, string $status): ?Slot
    {
        return collect($slots)
            ->filter(fn ($slot) => $slot->status() === $status)
            ->first();
    }
}
