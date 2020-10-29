<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\DTO\Slot;
use App\Facades\Network;
use App\Models\Block;
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
        $tracking = DelegateTracker::execute(Monitor::activeDelegates(Monitor::roundNumber()));

        $delegates = [];

        for ($i = 0; $i < count($tracking); $i++) {
            $delegate = array_values($tracking)[$i];

            $delegates[] = new Slot([
                'order'         => $i + 1,
                'wallet'        => ViewModelFactory::make(Cache::tags(['delegates'])->get($delegate['publicKey'])),
                'forging_at'    => Carbon::now()->addMilliseconds($delegate['time']),
                'last_block'    => Cache::get('lastBlock:'.$delegate['publicKey']),
                'is_success'    => false, // $missedCount === 0,
                'is_warning'    => false, // $missedCount === 1,
                'is_danger'     => false, // $missedCount >= 2,
                'missed_count'  => 0,
                'status'        => $delegate['status'],
                'time'          => $delegate['time'],
            ]);
        }

        return view('livewire.monitor-network', [
            'delegates'  => $delegates,
            'statistics' => [
                'blockCount'      => $this->blockCount($delegates),
                'transactions'    => $this->transactions(),
                'currentDelegate' => $this->currentDelegate($delegates),
                'nextDelegate'    => $this->nextDelegate($delegates),
            ],
        ]);
    }

    private function blockCount(array $delegates): string
    {
        return Cache::remember('MonitorNetwork:blockCount', Network::blockTime(), function () use ($delegates): string {
            return trans('pages.monitor.statistics.blocks_generated', [
                collect($delegates)->filter(fn ($slot) => $slot->status() === 'done')->count(),
                Network::delegateCount(),
            ]);
        });
    }

    private function transactions(): int
    {
        return Cache::remember('MonitorNetwork:transactions', Network::blockTime(), function (): int {
            return Block::whereBetween('height', Monitor::heightRangeByRound(Monitor::roundNumber()))->sum('number_of_transactions');
        });
    }

    private function currentDelegate(array $delegates): WalletViewModel
    {
        return Cache::remember('MonitorNetwork:currentDelegate', Network::blockTime(), function () use ($delegates): WalletViewModel {
            return $this->getSlotsByStatus($delegates, 'next')->wallet();
        });
    }

    private function nextDelegate(array $delegates): WalletViewModel
    {
        return Cache::remember('MonitorNetwork:nextDelegate', Network::blockTime(), function () use ($delegates): WalletViewModel {
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
