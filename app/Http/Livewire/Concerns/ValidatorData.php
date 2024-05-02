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

trait ValidatorData
{
    private function cacheLastBlocks(array $validators): void
    {
        $ttl = (int) ceil(Network::blockTime() / 2);

        Cache::remember('monitor:last-blocks', $ttl, function () use ($validators): void {
            $blocks = Block::query()
                ->orderBy('height', 'desc')
                ->limit(Network::validatorCount() * 2)
                ->get();

            foreach ($validators as $validator) {
                $block = $blocks->firstWhere('generator_public_key', $validator);

                // The validator hasn't forged in some rounds.
                if (is_null($block)) {
                    $block = Block::query()
                        ->where('generator_public_key', $validator)
                        ->orderBy('height', 'desc')
                        ->limit(1)
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
        });
    }

    private function getBlocksByRange(array $publicKeys, array $heightRange): Collection
    {
        $key = 'monitor:last-blocks:'.md5(implode(',', $publicKeys)).':'.$heightRange[0].'-'.$heightRange[1];
        $ttl = (int) ceil(Network::blockTime() / 2);

        return Cache::remember($key, $ttl, function () use ($publicKeys, $heightRange) {
            return Block::query()
                    ->whereIn('generator_public_key', $publicKeys)
                    ->whereBetween('height', $heightRange)
                    ->orderBy('height', 'asc')
                    ->get();
        });
    }

    private function hasRoundStarted(int $height): bool
    {
        return Cache::remember(
            'delegate:round:'.$height,
            Network::blockTime() / 2,
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
            $currentRound = Rounds::previous();
            if ($currentRound === null) {
                return [];
            }

            $heightRange   = Monitor::heightRangeByRound($currentRound);
            $validators    = $currentRound->validators;

            $this->cacheLastBlocks($validators);

            if (! $this->hasRoundStarted($heightRange[0])) {
                return [];
            }
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
                roundNumber: $currentRound->round
            );
        }

        return $validators;
    }
}
