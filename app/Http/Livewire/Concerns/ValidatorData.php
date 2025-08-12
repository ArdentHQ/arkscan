<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\DTO\Slot;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Block;
use App\Services\Cache\RequestScopedCache;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\Monitor;
use App\Services\Monitor\ValidatorTracker;
use App\Services\Timestamp;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

trait ValidatorData
{
    public const MISSED_INCREMENT_SECONDS = 2;

    private function cacheTtl(): int
    {
        return (int) ceil(Network::blockTime() / 2);
    }

    private function cacheLastBlocks(array $validators): void
    {
        Cache::remember('monitor:last-blocks', $this->cacheTtl(), function () use ($validators): bool {
            $blocks = Block::query()
                ->orderBy('number', 'desc')
                ->limit(Network::validatorCount() * 2)
                ->get();

            $lastBlockHashes = DB::connection('explorer')
                ->table('wallets')
                ->whereIn('address', $validators)
                ->select([
                    'address',
                    'last_block_hash' => function ($query) {
                        $query->select('hash')
                            ->from('blocks')
                            ->whereColumn('proposer', 'address')
                            ->orderBy('number', 'desc')
                            ->limit(1);
                    },
                ]);

            /** @var Collection $lastBlocks */
            $lastBlocks = Block::whereIn('hash', $lastBlockHashes->pluck('last_block_hash'))
                ->get()
                ->groupBy('proposer');

            foreach ($validators as $address) {
                $block = $blocks->firstWhere('proposer', $address);

                // The validator hasn't forged in some rounds.
                if (is_null($block) && $lastBlocks->has($address)) {
                    $block = $lastBlocks->get($address)
                        ->first();
                }

                // The validator has never forged.
                if (is_null($block)) {
                    continue;
                }

                (new WalletCache())->setLastBlock($address, [
                    'hash'                   => $block->hash,
                    'number'                 => $block->number->toNumber(),
                    'timestamp'              => $block->timestamp,
                    'proposer'               => $block->proposer,
                ]);
            }

            return true;
        });
    }

    private function getBlocksByRange(array $addresses, array $heightRange): Collection
    {
        return RequestScopedCache::remember('monitor:blocks-by-range', function () use ($addresses, $heightRange): Collection {
            return Block::query()
                ->whereIn('proposer', $addresses)
                ->whereBetween('number', $heightRange)
                ->orderBy('number', 'asc')
                ->get();
        });
    }

    private function hasRoundStarted(int $height): bool
    {
        return Cache::remember(
            'validator:round:'.$height,
            $this->cacheTtl(),
            fn () => Block::where('number', $height)->exists()
        );
    }

    private function fetchValidators(): array
    {
        $currentRound  = Rounds::current();
        $heightRange   = Monitor::heightRangeByRound($currentRound);
        $validators    = $currentRound->validators;

        $this->cacheLastBlocks($validators);

        if (! $this->hasRoundStarted($heightRange[0])) {
            return [];
        }

        $tracking        = ValidatorTracker::execute($validators, $heightRange[0]);
        $roundBlocks     = $this->getBlocksByRange(Arr::pluck($tracking, 'address'), $heightRange);
        $blockTimestamp  = $roundBlocks->last()->timestamp;
        $validators      = [];

        $roundBlockCount = $roundBlocks->groupBy('proposer')
            ->map(function ($blocks) {
                return count($blocks);
            });

        for ($i = 0; $i < count($tracking); $i++) {
            $validator = array_values($tracking)[$i];

            $validatorWallet = (new WalletCache())->getValidator($validator['address']);
            if ($validatorWallet === null) {
                continue;
            }

            /** @var WalletViewModel $walletViewModel */
            $walletViewModel = ViewModelFactory::make($validatorWallet);

            $validators[] = new Slot(
                address: $validator['address'],
                order: $i + 1,
                wallet: $walletViewModel,
                forgingAt: Timestamp::fromUnix($blockTimestamp)->addMilliseconds($validator['time']),
                lastBlock: (new WalletCache())->getLastBlock($validator['address']),
                status: $validator['status'],
                roundBlockCount: $roundBlockCount,
                roundNumber: $currentRound->round,
                secondsUntilForge: $validator['time'],
            );
        }

        return $validators;
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
            ->orderBy('number', 'desc')
            ->first();

        $heightRange = Monitor::heightRangeByRound(Rounds::current());

        /** @var ?Block $lastRoundBlock */
        $lastRoundBlock = Block::query()
            ->where('proposer', $lastSlot->address())
            ->where('number', '>=', $heightRange[0])
            ->orderBy('number', 'desc')
            ->first();

        if ($lastRoundBlock === null) {
            $lastSuccessfulForger = collect($this->validators)
                ->filter(fn (Slot $validator) => $validator->hasForged())
                ->last();

            /** @var ?Block $lastRoundBlock */
            $lastRoundBlock = Block::query()
                ->where('proposer', $lastSuccessfulForger->address())
                ->where('number', '>=', $heightRange[0])
                ->orderBy('number', 'desc')
                ->first();
        }

        if ($lastRoundBlock === null) {
            return [];
        }

        $overflowBlocks = Block::where('number', '>', $lastRoundBlock->number)
            ->orderBy('number', 'asc')
            ->get();

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
        if ($overflowBlocks->isNotEmpty()) {
            $lastTimestamp = $overflowBlocks->last()['timestamp'];
        }

        $overflowBlockCount = $overflowBlocks->groupBy('proposer')
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

            if ($validator->address() === $lastBlock->proposer) {
                $hasReachedFinalSlot = true;
            }

            $lastTimestamp = $forgingAt->unix();

            $previousStatus = $status;

            $overflowSlots[] = $slot;
        }

        return $overflowSlots;
    }
}
