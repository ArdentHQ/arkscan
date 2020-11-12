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
use App\Services\Timestamp;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Component;

final class DelegateMonitor extends Component
{
    private array $delegates = [];

    private array $statistics = [];

    public function render(): View
    {
        return view('livewire.delegate-monitor', [
            'delegates'  => $this->delegates,
            'statistics' => $this->statistics,
            'round'      => Rounds::current(),
        ]);
    }

    public function pollDelegates(): void
    {
        // $tracking = DelegateTracker::execute(Rounds::allByRound(112168));

        try {
            $roundNumber = Rounds::current();
            $heightRange = Monitor::heightRangeByRound($roundNumber);
            $delegates   = Rounds::allByRound($roundNumber);

            $this->cacheLastBlocks($delegates->pluck('public_key')->toArray());

            $tracking    = DelegateTracker::execute($delegates, $heightRange[0]);
            $roundBlocks = $this->getBlocksByRange(Arr::pluck($tracking, 'publicKey'), $heightRange);

            $delegates = [];

            for ($i = 0; $i < count($tracking); $i++) {
                $delegate = array_values($tracking)[$i];

                $delegates[] = new Slot([
                        'publicKey'  => $delegate['publicKey'],
                        'order'      => $i + 1,
                        'wallet'     => ViewModelFactory::make((new WalletCache())->getDelegate($delegate['publicKey'])),
                        'forging_at' => Timestamp::fromGenesis($roundBlocks->last()->timestamp)->addMilliseconds($delegate['time']),
                        'last_block' => (new WalletCache())->getLastBlock($delegate['publicKey']),
                        'status'     => $delegate['status'],
                    ], $roundBlocks, $roundNumber);
            }

            $this->delegates = $delegates;

            $this->statistics = [
                'blockCount'      => $this->getBlockCount(),
                'transactions'    => $this->getTransactions(),
                'currentDelegate' => $this->getCurrentDelegate(),
                'nextDelegate'    => $this->getNextDelegate(),
            ];
            // @codeCoverageIgnoreStart
        } catch (\Throwable $th) {
            // @README: If any errors occur we want to keep polling until we have a list of delegates
            $this->pollDelegates();
        }
        // @codeCoverageIgnoreEnd
    }

    private function getBlockCount(): string
    {
        return (new MonitorCache())->setBlockCount(function (): string {
            return trans('pages.delegates.statistics.blocks_generated', [
                Network::delegateCount() - (Monitor::heightRangeByRound(Monitor::roundNumber())[1] - Block::max('height')),
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

    private function getCurrentDelegate(): WalletViewModel
    {
        return (new MonitorCache())->setCurrentDelegate(function (): WalletViewModel {
            return $this->getSlotsByStatus($this->delegates, 'next')->wallet();
        });
    }

    private function getNextDelegate(): WalletViewModel
    {
        return (new MonitorCache())->setNextDelegate(function (): WalletViewModel {
            return $this->getSlotsByStatus($this->delegates, 'pending')->wallet();
        });
    }

    private function getBlocksByRange(array $publicKeys, array $heightRange): Collection
    {
        return Block::query()
            ->whereIn('generator_public_key', $publicKeys)
            ->whereBetween('height', $heightRange)
            ->orderBy('height', 'asc')
            ->get();
    }

    private function getSlotsByStatus(array $slots, string $status): Slot
    {
        return collect($slots)
            ->filter(fn ($slot) => $slot->status() === $status)
            ->first();
    }

    private function cacheLastBlocks(array $delegates): void
    {
        $ttl = (int) ceil(Network::blockTime() / 2);

        Cache::remember('monitor:last-blocks', $ttl, function () use ($delegates): void {
            $blocks = Block::query()
                ->orderBy('height', 'desc')
                ->limit(Network::delegateCount() * 2)
                ->get();

            foreach ($delegates as $delegate) {
                $block = $blocks->firstWhere('generator_public_key', $delegate);

                // The delegate hasn't forged in some rounds.
                if (is_null($block)) {
                    $block = Block::query()
                        ->where('generator_public_key', $delegate)
                        ->orderBy('height', 'desc')
                        ->limit(1)
                        ->first();
                }

                // The delegate has never forged.
                if (is_null($block)) {
                    continue;
                }

                (new WalletCache())->setLastBlock($delegate, [
                    'id'                   => $block->id,
                    'height'               => $block->height->toNumber(),
                    'timestamp'            => Timestamp::fromGenesis($block->timestamp)->unix(),
                    'generator_public_key' => $block->generator_public_key,
                ]);
            }
        });
    }
}
