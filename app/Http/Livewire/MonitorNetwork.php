<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\DTO\Slot;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Block;
use App\Services\Cache\MonitorCache;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\DelegateTracker;
use App\Services\Monitor\Monitor;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

final class MonitorNetwork extends Component
{
    public function render(): View
    {
        // $tracking = DelegateTracker::execute(Monitor::roundDelegates(112168));

        $roundNumber = Rounds::currentRound()->round;
        $heightRange = Monitor::heightRangeByRound($roundNumber);
        $tracking    = DelegateTracker::execute(Rounds::allByRound($roundNumber));
        $roundBlocks = $this->getBlocksByRange(Arr::pluck($tracking, 'publicKey'), $heightRange);

        $delegates = [];

        for ($i = 0; $i < count($tracking); $i++) {
            $delegate = array_values($tracking)[$i];

            $delegates[] = new Slot([
                'publicKey'  => $delegate['publicKey'],
                'order'      => $i + 1,
                'wallet'     => ViewModelFactory::make((new WalletCache())->getDelegate($delegate['publicKey'])),
                'forging_at' => Carbon::now()->addMilliseconds($delegate['time']),
                'last_block' => (new WalletCache())->getLastBlock($delegate['publicKey']),
                'status'     => $delegate['status'],
            ], $roundBlocks);
        }

        return view('livewire.monitor-network', [
            'delegates'  => $delegates,
            'statistics' => [
                'blockCount'      => $this->getBlockCount($delegates),
                'transactions'    => $this->getTransactions(),
                'currentDelegate' => $this->getCurrentDelegate($delegates),
                'nextDelegate'    => $this->getNextDelegate($delegates),
            ],
        ]);
    }

    private function getBlockCount(array $delegates): string
    {
        return (new MonitorCache())->setBlockCount(function () use ($delegates): string {
            return trans('pages.monitor.statistics.blocks_generated', [
                collect($delegates)->filter(fn ($slot) => $slot->status() === 'done')->count(),
                Network::delegateCount(),
            ]);
        });
    }

    private function getTransactions(): int
    {
        return (new MonitorCache())->setTransactions(function (): int {
            return (int) Block::whereBetween('height', Monitor::heightRangeByRound(Monitor::roundNumber()))->sum('number_of_transactions');
        });
    }

    private function getCurrentDelegate(array $delegates): WalletViewModel
    {
        return (new MonitorCache())->setCurrentDelegate(function () use ($delegates): WalletViewModel {
            return $this->getSlotsByStatus($delegates, 'next')->wallet();
        });
    }

    private function getNextDelegate(array $delegates): WalletViewModel
    {
        return (new MonitorCache())->setNextDelegate(function () use ($delegates): WalletViewModel {
            return $this->getSlotsByStatus($delegates, 'pending')->wallet();
        });
    }

    private function getBlocksByRange(array $publicKeys, array $heightRange): Collection
    {
        return Block::query()
            ->whereIn('generator_public_key', $publicKeys)
            ->whereBetween('height', $heightRange)
            ->get();
    }

    private function getSlotsByStatus(array $slots, string $status): Slot
    {
        return collect($slots)
            ->filter(fn ($slot) => $slot->status() === $status)
            ->first();
    }
}
