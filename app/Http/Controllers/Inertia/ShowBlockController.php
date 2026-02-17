<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\BlockDetails;
use App\DTO\Inertia\Transaction as TransactionDTO;
use App\Http\Controllers\Inertia\Concerns\WithPagination;
use App\Models\Block;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Scopes\OrderByTransactionIndexScope;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

final class ShowBlockController
{
    use WithPagination;

    public function __invoke(Block $block): Response
    {
        return Inertia::render('Block/Show', [
            'block' => BlockDetails::fromModel($block),

            'transactions' => Inertia::optional(function () use ($block) {
                $paginator = $this->getTransactions($block);

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->noResultsMessage($paginator->count()),
                ];
            }),
        ])->withMeta('block', [
            'blockid' => $block->hash,
        ]);
    }

    public function noResultsMessage(int $count): null|string
    {
        if ($count === 0) {
            return trans('tables.transactions.no_results.no_results');
        }

        return null;
    }

    public function getTransactions(Block $block): LengthAwarePaginator
    {
        $transactionCount = $block->transactions_count;

        if ($transactionCount === 0) {
            return new LengthAwarePaginator([], 0, $this->perPage(), $this->page(), [
                'path'     => route('block', $block),
                'pageName' => 'page',
            ]);
        }

        $transactions = $block
            ->transactions()
            ->withScope(OrderByTimestampScope::class)
            ->withScope(OrderByTransactionIndexScope::class)
            ->with(['votedFor', 'sender', 'senderWallet', 'recipientWallet'])
            ->forPage($this->page(), $this->perPage())
            ->get();

        return (new LengthAwarePaginator($transactions, $transactionCount, $this->perPage(), $this->page(), [
            'path'     => route('block', $block),
            'pageName' => 'page',
        ]))->through(fn ($transaction) => TransactionDTO::fromModel($transaction));
    }
}
