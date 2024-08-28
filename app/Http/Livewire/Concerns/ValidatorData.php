<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\DTO\Slot;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Block;
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
    private function cacheTtl(): int
    {
        return (int) ceil(Network::blockTime() / 2);
    }

    private function cacheLastBlocks(array $validators): void
    {
        Cache::remember('monitor:last-blocks', $this->cacheTtl(), function () use ($validators): bool {
            $blocks = Block::query()
                ->orderBy('height', 'desc')
                ->limit(Network::validatorCount() * 2)
                ->get();

            $lastBlockIds = DB::connection('explorer')
                ->table('wallets')
                ->whereIn('public_key', $validators)
                ->select([
                    'public_key',
                    'last_block_id' => function ($query) {
                        $query->select('id')
                            ->from('blocks')
                            ->whereColumn('generator_public_key', 'public_key')
                            ->orderBy('height', 'desc')
                            ->limit(1);
                    },
                ]);

            /** @var Collection $lastBlocks */
            $lastBlocks = Block::whereIn('id', $lastBlockIds->pluck('last_block_id'))
                ->get()
                ->groupBy('generator_public_key');

            foreach ($validators as $validator) {
                $block = $blocks->firstWhere('generator_public_key', $validator);

                // The validator hasn't forged in some rounds.
                if (is_null($block) && $lastBlocks->has($validator)) {
                    $block = $lastBlocks->get($validator)
                        ->first();
                }

                // The validator has never forged.
                if (is_null($block)) {
                    continue;
                }

                (new WalletCache())->setLastBlock($validator, [
                    'id'                   => $block->id,
                    'height'               => $block->height->toNumber(),
                    'timestamp'            => $block->timestamp,
                    'generator_public_key' => $block->generator_public_key,
                ]);
            }

            return true;
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

    private function hasRoundStarted(int $height): bool
    {
        return Cache::remember(
            'delegate:round:'.$height,
            $this->cacheTtl(),
            fn () => Block::where('height', $height)->exists()
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
        $roundBlocks     = $this->getBlocksByRange(Arr::pluck($tracking, 'publicKey'), $heightRange);
        $blockTimestamp  = $roundBlocks->last()->timestamp;
        $validators      = [];

        $roundBlockCount = $roundBlocks->groupBy('generator_public_key')
            ->map(function ($blocks) {
                return count($blocks);
            });

        for ($i = 0; $i < count($tracking); $i++) {
            $validator = array_values($tracking)[$i];

            $validatorWallet = (new WalletCache())->getValidator($validator['publicKey']);
            if ($validatorWallet === null) {
                continue;
            }

            /** @var WalletViewModel $walletViewModel */
            $walletViewModel = ViewModelFactory::make($validatorWallet);

            $validators[] = new Slot(
                publicKey: $validator['publicKey'],
                order: $i + 1,
                wallet: $walletViewModel,
                forgingAt: Timestamp::fromUnix($blockTimestamp)->addMilliseconds($validator['time']),
                lastBlock: (new WalletCache())->getLastBlock($validator['publicKey']),
                status: $validator['status'],
                roundBlockCount: $roundBlockCount,
                roundNumber: $currentRound->round,
                secondsUntilForge: $validator['time'],
            );
        }

        return $validators;
    }
}
