<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Block as BlockDTO;
use App\Http\Controllers\Inertia\Concerns\WithPagination;
use App\Models\Block;
use App\Models\ForgingStats;
use App\Models\Scopes\OrderByTimestampScope;
use App\Services\BigNumber;
use App\Services\Timestamp;
use ARKEcosystem\Foundation\UserInterface\UI;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

final class BlocksListController
{
    use WithPagination;

    public const STATS_TTL = 300;

    public function __invoke(): Response
    {
        $data = $this->blockData();

        return Inertia::render('Blocks/List', [
            'statistics' => [
                'forgedCount'     => $data['block_count'],
                'missedCount'     => $data['missed_count'],
                'totalRewards'    => BigNumber::new($data['total_rewards'])->toFloat(),
                'maxTransactions' => $data['max_transactions'],
            ],

            'blocks' => Inertia::optional(function () {
                $paginator = $this->getBlocks();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->noResultsMessage($paginator->count()),
                ];
            }),
        ]);
    }

    public function noResultsMessage(int $count): null|string
    {
        if ($count === 0) {
            return trans('tables.blocks.no_results');
        }

        return null;
    }

    public function getBlocks(): LengthAwarePaginator
    {
        /** @var ?Block $lastBlock */
        $lastBlock = Block::withScope(OrderByTimestampScope::class)->first();

        if ($lastBlock === null) {
            return new LengthAwarePaginator([], 0, $this->perPage(), $this->page(), [
                'path'     => route('blocks'),
                'pageName' => 'page',
            ]);
        }

        /** @var Block $firstBlock */
        $firstBlock = Block::withScope(OrderByTimestampScope::class, 'asc')->first();

        $lastBlockHeight = $lastBlock->number->toNumber();
        $blockCount      = $lastBlockHeight;

        $firstBlockHeight = $firstBlock->number->toNumber();
        if ($firstBlockHeight > 1) {
            $blockCount -= $firstBlockHeight - 1; // Adjust for the first block if it's not the genesis block
        }

        $heightTo   = $lastBlockHeight - ($this->perPage() * ($this->page() - 1));
        $heightFrom = $heightTo - $this->perPage();

        $blocks = Block::withScope(OrderByTimestampScope::class)
            ->where('number', '<=', $heightTo)
            ->where('number', '>', $heightFrom)
            ->get();

        return (new LengthAwarePaginator($blocks, $blockCount, $this->perPage(), $this->page(), [
            'path'     => route('blocks'),
            'pageName' => 'page',
        ]))->through(fn (Block $block) => BlockDTO::fromModel($block));
    }

    private function blockData(): array
    {
        return Cache::remember('blocks:stats', self::STATS_TTL, function () {
            $timestamp = Timestamp::fromUnix(Carbon::now()->subDays(1)->unix())->unix();

            $data      = (array) DB::connection('explorer')
                ->table('blocks')
                ->selectRaw('COUNT(blocks.*) as block_count')
                ->selectRaw('SUM(reward) as total_rewards')
                ->selectRaw('MAX(transactions_count) as max_transactions')
                ->where('blocks.timestamp', '>', $timestamp * 1000)
                ->first();

            return [
                'block_count'      => $data['block_count'],
                'missed_count'     => ForgingStats::missed()->where('timestamp', '>', $timestamp)->count(),
                'total_rewards'    => $data['total_rewards'] ?? 0,
                'max_transactions' => $data['max_transactions'] ?? 0,
            ];
        });
    }
}
