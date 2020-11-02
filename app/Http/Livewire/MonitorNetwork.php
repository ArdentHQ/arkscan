<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\DTO\Slot;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Jobs\CacheLastBlockByPublicKey;
use App\Models\Block;
use App\Services\Cache\MonitorCache;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\DelegateTracker;
use App\Services\Monitor\Monitor;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
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

        $delegates = [];

        for ($i = 0; $i < count($tracking); $i++) {
            $delegate = array_values($tracking)[$i];

            // if (Cache::missing('lastBlock:'.$delegate['publicKey'])) {
            //     CacheLastBlockByPublicKey::dispatchSync($delegate['publicKey']);
            // }

            $delegates[] = new Slot([
                'publicKey'  => $delegate['publicKey'],
                'order'      => $i + 1,
                'wallet'     => ViewModelFactory::make(Cache::tags(['delegates'])->get($delegate['publicKey'])),
                'forging_at' => Carbon::now()->addMilliseconds($delegate['time']),
                'last_block' => (new WalletCache())->getLastBlock($delegate['publicKey']),
                'status'     => $delegate['status'],
            ], $heightRange);
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

    public function getBlockCount(array $delegates): string
    {
        return (new MonitorCache())->setBlockCount(function () use ($delegates): string {
            return trans('pages.monitor.statistics.blocks_generated', [
                collect($delegates)->filter(fn ($slot) => $slot->status() === 'done')->count(),
                Network::delegateCount(),
            ]);
        });
    }

    public function getTransactions(): int
    {
        return (new MonitorCache())->setTransactions(function (): int {
            return (int) Block::whereBetween('height', Monitor::heightRangeByRound(Monitor::roundNumber()))->sum('number_of_transactions');
        });
    }

    public function getCurrentDelegate(array $delegates): WalletViewModel
    {
        return (new MonitorCache())->setCurrentDelegate(function () use ($delegates): WalletViewModel {
            return $this->getSlotsByStatus($delegates, 'next')->wallet();
        });
    }

    public function getNextDelegate(array $delegates): WalletViewModel
    {
        return (new MonitorCache())->setNextDelegate(function () use ($delegates): WalletViewModel {
            return $this->getSlotsByStatus($delegates, 'pending')->wallet();
        });
    }

    private function getSlotsByStatus(array $slots, string $status): Slot
    {
        return collect($slots)
            ->filter(fn ($slot) => $slot->status() === $status)
            ->first();
    }
}
