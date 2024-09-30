<?php

declare(strict_types=1);

namespace App\Http\Livewire\Delegates;

use App\Actions\CacheNetworkHeight;
use App\DTO\Slot;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\DelegateData;
use App\Http\Livewire\Delegates\Concerns\HandlesMonitorDataBoxes;
use App\Models\Block;
use App\Services\Monitor\Monitor as MonitorService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Livewire\Component;
use Throwable;

/**
 * @property bool $hasDelegates
 * @property array $overflowDelegates
 */
final class Monitor extends Component
{
    use DeferLoading;
    use DelegateData;
    use HandlesMonitorDataBoxes;

    public const MISSED_INCREMENT_SECONDS = 2;

    /** @var mixed */
    protected $listeners = [
        'monitorIsReady',
        'echo:blocks,NewBlock' => 'pollData',
    ];

    private array $delegates = [];

    public function render(): View
    {
        $height = 0;
        if ($this->isReady) {
            $height = CacheNetworkHeight::execute();
        }

        return view('livewire.delegates.monitor', [
            'round'             => Rounds::current(),
            'delegates'         => $this->delegates,
            'overflowDelegates' => $this->overflowDelegates,
            'height'            => $height,
            'statistics'        => $this->statistics,
        ]);
    }

    public function monitorIsReady(): void
    {
        $this->setIsReady();

        $this->pollDelegates();
        $this->pollStatistics();
    }

    public function getHasDelegatesProperty(): bool
    {
        return count($this->delegates) > 0;
    }

    public function pollData(): void
    {
        $this->pollDelegates();
        $this->pollStatistics();
    }

    public function pollDelegates(): void
    {
        if (! $this->isReady) {
            return;
        }

        try {
            $this->delegates = $this->fetchDelegates();

            Cache::forget('poll-delegates-exception-occurrence');
        } catch (Throwable $e) {
            $occurrences = Cache::increment('poll-delegates-exception-occurrence');

            if ($occurrences >= 3) {
                throw $e;
            }

            // @README: If any errors occur we want to keep polling until we have a list of delegates
            $this->pollDelegates();
        }
    }

    /**
     * Calculate final overflow of delegates based on missed blocks for current round.
     *
     * @return array<Slot>
     */
    public function getOverflowDelegatesProperty(): array
    {
        // dump(collect($this->delegates)->map(fn ($d) => $d->publicKey())->toArray());

        $missedCount = collect($this->delegates)
            ->filter(fn ($delegate) => $delegate->justMissed())
            ->count();

        /** @var ?Slot $lastSlot */
        $lastSlot   = collect($this->delegates)->last();
        $lastStatus = $lastSlot?->status() ?? 'pending';

        // dump('missedCount', $missedCount);

        if ($lastSlot === null) {
            return [];
        }

        /** @var Block $lastBlock */
        $lastBlock = Block::query()
            ->orderBy('height', 'desc')
            ->first();

        $heightRange = MonitorService::heightRangeByRound(Rounds::current());

        /** @var ?Block $lastRoundBlock */
        $lastRoundBlock = Block::query()
            ->where('generator_public_key', $lastSlot->publicKey())
            ->where('height', '>=', $heightRange[0])
            ->orderBy('height', 'desc')
            ->first();

        // dump('lastRoundBlock', $lastRoundBlock?->height);

        // collect($this->delegates)
        //     ->each(fn (Slot $delegate, $index) => dump($index.': '.$delegate->status().': '.$delegate->order().'-'.$delegate->publicKey().'-'.($delegate?->lastBlock()['height'] ?? null)));

        // $lastRoundBlock = null;
        if ($lastRoundBlock === null) {
            // dump('yo');

            $lastSuccessfulForger = collect($this->delegates)
                ->filter(fn (Slot $delegate) => $delegate->hasForged())
                ->last();

            if ($lastSuccessfulForger === null) {
                return [];
            }

            /** @var Block $lastRoundBlock */
            $lastRoundBlock = Block::query()
                ->where('generator_public_key', $lastSuccessfulForger->publicKey())
                ->where('height', '>=', $heightRange[0])
                ->orderBy('height', 'desc')
                ->first();
        }

        $overflowBlocks = Block::where('height', '>', $lastRoundBlock->height)
            ->orderBy('height', 'asc')
            ->get();

        // dump('lastStatus', $lastStatus);
        // dump('overflowBlocks', $overflowBlocks->count(), $lastRoundBlock->height, $heightRange);

        if ($lastStatus !== 'done' || $overflowBlocks->isEmpty()) {
            return $this->getOverflowSlots(
                $missedCount,
                $lastStatus,
                $lastBlock,
                $lastSlot->forgingAt()->unix(),
                hasReachedFinalSlot: true,
            );
        }

        $lastTimestamp = $lastRoundBlock->timestamp;
        if ($overflowBlocks->isNotEmpty() && $overflowBlocks->last() !== null) {
            $lastTimestamp = Timestamp::fromGenesis($overflowBlocks->last()['timestamp'])->unix();
        }

        $overflowBlockCount = $overflowBlocks->groupBy('generator_public_key')
            ->map(function ($blocks) {
                return count($blocks);
            });

        $hasReachedFinalSlot = $lastTimestamp === $lastRoundBlock->timestamp;

        $overflowSlots = $this->getOverflowSlots(
            $missedCount,
            $lastStatus,
            $lastBlock,
            $lastTimestamp,
            overflowBlockCount: $overflowBlockCount,
            hasReachedFinalSlot: $hasReachedFinalSlot,
        );

        $additional = 0;
        foreach ($overflowSlots as $slot) {
            if (! $slot->justMissed()) {
                continue;
            }

            $additional++;
        }

        if ($additional === 0) {
            return $overflowSlots;
        }

        return $this->getOverflowSlots(
            $missedCount + $additional,
            $lastStatus,
            $lastBlock,
            $lastTimestamp,
            overflowBlockCount: $overflowBlockCount,
            hasReachedFinalSlot: $hasReachedFinalSlot,
        );
    }

    /**
     * Get overflow slots based on current round data.
     * Used multiple times to determine if an overflow slot has been missed.
     *
     * @return array<Slot>
     */
    private function getOverflowSlots(
        int $missedCount,
        string $previousStatus,
        Block $lastBlock,
        int $lastTimestamp,
        ?Collection $overflowBlockCount = null,
        bool $hasReachedFinalSlot = false,
    ): array {
        if ($overflowBlockCount === null) {
            $overflowBlockCount = new Collection();
        }

        $justMissedCount = 0;
        $overflowSlots   = [];
        foreach (collect($this->delegates)->take($missedCount) as $delegate) {
            if ($overflowBlockCount->isEmpty()) {
                $secondsUntilForge = Network::blockTime();

                $forgingAt = Carbon::createFromTimestamp($lastTimestamp)->addSeconds($secondsUntilForge);
            } else {
                $secondsUntilForge = Network::blockTime();

                $forgingAt = Carbon::createFromTimestamp($lastTimestamp)->addSeconds($secondsUntilForge);
            }

            $status = 'pending';
            if (! $hasReachedFinalSlot) {
                $status = 'done';
            } elseif ($previousStatus === 'done') {
                $status = 'next';
            }

            $slot = $delegate->clone(
                secondsUntilForge: $secondsUntilForge,
                forgingAt: $forgingAt,
                status: $status,
                roundBlockCount: $overflowBlockCount,
            );

            if ($slot->justMissed()) {
                $justMissedCount++;
            } else {
                $justMissedCount = 0;
            }

            if ($delegate->publicKey() === $lastBlock->generator_public_key) {
                $hasReachedFinalSlot = true;
            }

            $lastTimestamp = $forgingAt->unix();

            $previousStatus = $status;

            $overflowSlots[] = $slot;
        }

        return $overflowSlots;
    }
}
