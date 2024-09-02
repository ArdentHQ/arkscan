<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators;

use App\DTO\Slot;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\ValidatorData;
use App\Models\Block;
use App\Services\Monitor\Monitor as MonitorService;
use App\Services\Timestamp;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Component;
use Throwable;

/**
 * @property bool $hasValidators
 * @property array $overflowValidators
 */
final class Monitor extends Component
{
    use DeferLoading;
    use ValidatorData;

    public const MISSED_INCREMENT_SECONDS = 2;

    /** @var mixed */
    protected $listeners = [
        'monitorIsReady',
    ];

    private array $validators = [];

    public function render(): View
    {
        return view('livewire.validators.monitor', [
            'round'              => Rounds::current()->round,
            'validators'         => $this->validators,
            'overflowValidators' => $this->getOverflowValidatorsProperty(),
        ]);
    }

    public function monitorIsReady(): void
    {
        $this->setIsReady();

        $this->pollValidators();
    }

    public function getHasValidatorsProperty(): bool
    {
        return count($this->validators) > 0;
    }

    public function pollValidators(): void
    {
        if (! $this->isReady) {
            return;
        }

        try {
            $this->validators = $this->fetchValidators();

            Cache::forget('poll-validators-exception-occurrence');
        } catch (Throwable $e) {
            $occurrences = Cache::increment('poll-validators-exception-occurrence');

            if ($occurrences >= 3) {
                throw $e;
            }

            // @README: If any errors occur we want to keep polling until we have a list of validators
            $this->pollValidators();
        }
    }

    /**
     * Calculate final overflow of validators based on missed blocks for current round.
     *
     * @return array<Slot>
     */
    public function getOverflowValidatorsProperty(): array
    {
        $missedCount = collect($this->validators)
            ->filter(fn ($validator) => $validator->justMissed())
            ->count();

        /** @var ?Slot $lastSlot */
        $lastSlot   = collect($this->validators)->last();
        $lastStatus = $lastSlot?->status() ?? 'pending';

        if ($lastSlot === null) {
            return [];
        }

        /** @var Block $lastBlock */
        $lastBlock = Block::query()
            ->orderBy('height', 'desc')
            ->first();

        $heightRange = MonitorService::heightRangeByRound(Rounds::current());

        $lastRoundBlock = Block::query()
            ->where('generator_public_key', $lastSlot->publicKey())
            ->where('height', '>=', $heightRange[0])
            ->orderBy('height', 'desc')
            ->first();

        if ($lastRoundBlock === null) {
            $lastSuccessfulForger = collect($this->validators)
                ->filter(fn (Slot $validator) => $validator->hasForged())
                ->last();

            $lastRoundBlock = Block::query()
                ->where('generator_public_key', $lastSuccessfulForger->publicKey())
                ->where('height', '>=', $heightRange[0])
                ->orderBy('height', 'desc')
                ->first();
        }

        if ($lastRoundBlock === null) {
            return [];
        }

        $overflowBlocks = Block::where('height', '>', $lastRoundBlock->height)
            ->orderBy('height', 'asc')
            ->get();

        if ($lastStatus !== 'done' || $overflowBlocks->isEmpty()) {
            // return [];

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
            $lastTimestamp = $overflowBlocks->last()['timestamp'];
        }

        $overflowBlockCount = $overflowBlocks->groupBy('generator_public_key')
            ->map(function ($blocks) {
                return count($blocks);
            });

        $hasReachedFinalSlot = $lastTimestamp === $lastRoundBlock->timestamp;

        Log::debug([
            'lastBlock'           => $lastBlock,
            'missedCount'         => $missedCount,
            'lastStatus'          => $lastStatus,
            'lastTimestamp'       => $lastTimestamp,
            'overflowBlockCount'  => collect($overflowBlockCount)->toArray(),
            'hasReachedFinalSlot' => $hasReachedFinalSlot,
        ]);

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

        // return [];

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
        $missedSeconds   = 0;
        $overflowSlots   = [];
        foreach (collect($this->validators)->take($missedCount) as $index => $validator) {
            if ($overflowBlockCount->isEmpty()) {
                $secondsUntilForge = Network::blockTime();

                $forgingAt = Timestamp::fromUnix($lastTimestamp)->addSeconds($secondsUntilForge);
            } else {
                $secondsUntilForge = Network::blockTime();
                $secondsUntilForge += $missedSeconds;

                $forgingAt = Timestamp::fromUnix($lastTimestamp)->addSeconds($secondsUntilForge);
            }

            $status = 'pending';
            if (! $hasReachedFinalSlot) {
                $status = 'done';
            } elseif ($previousStatus === 'done') {
                $status = 'next';
            }

            $slot = $validator->clone(
                secondsUntilForge: $secondsUntilForge,
                forgingAt: $forgingAt,
                status: $status,
                roundBlockCount: $overflowBlockCount,
            );

            if ($slot->justMissed()) {
                $justMissedCount++;
                $missedSeconds = $justMissedCount * self::MISSED_INCREMENT_SECONDS;
            } else {
                $justMissedCount = 0;
                $missedSeconds   = 0;
            }

            if ($validator->publicKey() === $lastBlock->generator_public_key) {
                $hasReachedFinalSlot = true;
            }

            $lastTimestamp = $forgingAt->unix();

            $previousStatus = $status;

            $overflowSlots[] = $slot;
        }

        return $overflowSlots;
    }
}
