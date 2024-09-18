<?php

declare(strict_types=1);

namespace App\Http\Livewire\Concerns;

use App\DTO\Slot;
use App\Facades\Network;
use App\Facades\Rounds;
use App\Models\Block;
use App\Services\Cache\WalletCache;
use App\Services\Monitor\DelegateTracker;
use App\Services\Monitor\Monitor;
use App\Services\Timestamp;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

trait DelegateData
{
    private function cacheTtl(): int
    {
        return (int) ceil(Network::blockTime() / 2);
    }

    private function cacheLastBlocks(array $delegates): void
    {
        Cache::remember('monitor:last-blocks', $this->cacheTtl(), function () use ($delegates): bool {
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

    private function fetchDelegates(): array
    {
        $roundNumber = Rounds::current();
        $heightRange = Monitor::heightRangeByRound($roundNumber);
        $delegates   = Rounds::allByRound($roundNumber);

        $this->cacheLastBlocks($delegates->pluck('public_key')->toArray());

        if (! $this->hasRoundStarted($heightRange[0])) {
            return [];
        }

        $tracking       = DelegateTracker::execute($delegates, $heightRange[0]);
        $roundBlocks    = $this->getBlocksByRange(Arr::pluck($tracking, 'publicKey'), $heightRange);
        $blockTimestamp = $roundBlocks->last()->timestamp;
        $delegates      = [];

        $roundBlockCount = $roundBlocks->groupBy('generator_public_key')
            ->map(function ($blocks) {
                return count($blocks);
            });

        for ($i = 0; $i < count($tracking); $i++) {
            $delegate = array_values($tracking)[$i];

            $delegateWallet = (new WalletCache())->getDelegate($delegate['publicKey']);
            if ($delegateWallet === null) {
                continue;
            }

            /** @var WalletViewModel $walletViewModel */
            $walletViewModel = ViewModelFactory::make($delegateWallet);

            $delegates[] = new Slot(
                publicKey: $delegate['publicKey'],
                order: $i + 1,
                wallet: $walletViewModel,
                forgingAt: Timestamp::fromGenesis($blockTimestamp)->addMilliseconds($delegate['time']),
                lastBlock: (new WalletCache())->getLastBlock($delegate['publicKey']),
                status: $delegate['status'],
                roundBlockCount: $roundBlockCount,
                roundNumber: $roundNumber,
                secondsUntilForge: $delegate['time'],
            );
        }

        return $delegates;
    }
}
