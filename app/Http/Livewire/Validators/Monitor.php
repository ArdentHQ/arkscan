<?php

declare(strict_types=1);

namespace App\Http\Livewire\Validators;

use App\DTO\Slot;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\ValidatorData;
use App\Models\Block;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
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

    const MISSED_INCREMENT_SECONDS = 2;

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

    public function getOverflowValidatorsProperty(): array
    {
        $missedCount = collect($this->validators)
            ->filter(fn ($validator) => $validator->justMissed())
            ->count();

        /** @var ?Slot $previousSlot */
        $previousSlot   = collect($this->validators)->last();
        $previousStatus = $previousSlot?->status() ?? 'pending';

        if ($previousSlot === null) {
            return [];
        }

        /** @var Block $lastBlock */
        $lastBlock = Block::query()
            ->orderBy('height', 'desc')
            ->first();

        if ($previousSlot->status() !== 'done') {
            return $this->getOverflowSlots(
                $missedCount,
                $previousStatus,
                $lastBlock,
                0,
                Network::blockTime(),
            );
        }

        $lastRoundBlock = null;
        $lastRoundBlock = collect($this->validators)
            ->filter(fn (Slot $validator) => $validator->hasForged())
            ->last()
            ->lastBlock();

        $overflowBlocks = Block::where('height', '>', $lastRoundBlock['height'])
            ->orderBy('height', 'desc')
            ->get();

        $overflowBlockCount = $overflowBlocks->groupBy('generator_public_key')
            ->map(function ($blocks) {
                return count($blocks);
            });

        $lastTimestamp       = 0;
        $lastSlotForgedIndex = null;

        /**
         * @var int $index
         * @var Slot $slot
         */
        foreach ($this->validators as $index => $slot) {
            if ($slot->lastBlock()['id'] !== $lastRoundBlock['id']) {
                continue;
            }

            $lastSlotForgedIndex = $index;
        }

        if ($lastSlotForgedIndex !== null) {
            $lastTimestamp = $lastRoundBlock['timestamp'] + ((Network::validatorCount() - $lastSlotForgedIndex) * Network::blockTime());
        }

        $overflowSlots = $this->getOverflowSlots(
            $missedCount,
            $previousStatus,
            $lastBlock,
            $lastTimestamp,
            0,
            $overflowBlockCount,
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
            $previousStatus,
            $lastBlock,
            $lastTimestamp,
            0,
            $overflowBlockCount,
        );
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
     * @return array<Slot>
     */
    private function getOverflowSlots(
        int $missedCount,
        string $previousStatus,
        Block $lastBlock,
        int $lastTimestamp,
        int $secondsUntilOverflow = 0,
        ?Collection $overflowBlockCount = null,
    ): array
    {
        if ($overflowBlockCount === null) {
            $overflowBlockCount = new Collection();
        }

        $justMissedCount = 0;

        $hasHitLastForger = false;

        $overflowSlots = [];
        foreach (collect($this->validators)->take($missedCount) as $index => $validator) {
            if ($overflowBlockCount->isEmpty()) {
                $secondsUntilForge = $secondsUntilOverflow + ($index * Network::blockTime());

                $forgingAt = Timestamp::fromUnix($lastTimestamp)->addSeconds($secondsUntilForge);
            } else {
                $secondsUntilForge = Network::blockTime() + ($justMissedCount * Network::blockTime());
                $secondsUntilForge += $justMissedCount * self::MISSED_INCREMENT_SECONDS;

                $forgingAt = Timestamp::fromUnix($lastBlock->timestamp)->addSeconds($secondsUntilForge);
            }

            $status = 'pending';
            if (! $hasHitLastForger && $forgingAt < Carbon::now()) {
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
            } else {
                $justMissedCount = 0;
            }

            if ($validator->publicKey() === $lastBlock->generator_public_key) {
                $hasHitLastForger = true;
            }

            $previousStatus = $status;

            $overflowSlots[] = $slot;

        }

        return $overflowSlots;
    }
}
