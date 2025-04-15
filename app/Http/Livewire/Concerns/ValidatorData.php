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

            $lastBlockIds = DB::connection('explorer')
                ->table('wallets')
                ->whereIn('address', $validators)
                ->select([
                    'address',
                    'last_block_id' => function ($query) {
                        $query->select('id')
                            ->from('blocks')
                            ->whereColumn('proposer', 'address')
                            ->orderBy('number', 'desc')
                            ->limit(1);
                    },
                ]);

            /** @var Collection $lastBlocks */
            $lastBlocks = Block::whereIn('id', $lastBlockIds->pluck('last_block_id'))
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
                    'id'                   => $block->id,
                    'number'               => $block->number->toNumber(),
                    'timestamp'            => $block->timestamp,
                    'proposer'    => $block->proposer,
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
}
