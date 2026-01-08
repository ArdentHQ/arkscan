<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Block as BlockDTO;
use App\DTO\Inertia\Transaction as TransactionDTO;
use App\Facades\Network;
use App\Models\Block;
use App\Models\Scopes\OrderByHeightScope;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

final class HomeController
{
    public function __invoke(): Response
    {
        return Inertia::render('Home/Index', [
            'transactions' => Inertia::optional(function () {
                $paginator = $this->getTransactions();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->noTransactionsResultsMessage($paginator->total()),
                ];
            }),

            'blocks' => Inertia::optional(function () {
                $paginator = $this->getBlocks();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->noBlocksResultsMessage($paginator->total()),
                ];
            }),

            'baseUrl' => route('home', absolute: false),
        ])->withMeta('home', [
            'name' => Network::currency(),
        ]);
    }

    public function noTransactionsResultsMessage(int $total): ?string
    {
        return $total === 0
            ? (string) trans('tables.transactions.no_results.no_results')
            : null;
    }

    public function noBlocksResultsMessage(int $total): ?string
    {
        return $total === 0
            ? (string) trans('tables.blocks.no_results')
            : null;
    }

    public function getTransactions(): LengthAwarePaginator
    {
        return Transaction::query()
            ->with('votedFor')
            ->withScope(OrderByTimestampScope::class)
            ->paginate((int) config('arkscan.pagination.per_page'))
            ->through(fn (Transaction $transaction) => TransactionDTO::fromModel($transaction));
    }

    public function getBlocks(): LengthAwarePaginator
    {
        return Block::withScope(OrderByHeightScope::class)
            ->paginate((int) config('arkscan.pagination.per_page'))
            ->through(fn (Block $block) => BlockDTO::fromModel($block));
    }
}
