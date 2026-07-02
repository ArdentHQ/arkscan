<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Block as BlockDTO;
use App\DTO\Inertia\Transaction as TransactionDTO;
use App\Http\Controllers\Inertia\Concerns\WithPagination;
use App\Models\Block;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Scopes\OrderByTransactionIndexScope;
use App\Models\Wallet;
use App\ViewModels\TransactionViewModel;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

final class ShowBlockController
{
    use WithPagination;

    public function __invoke(Block $block): Response
    {
        return Inertia::renderWithMeta('Block/Show', 'block', [
            'block' => BlockDTO::fromModel($block),

            'transactions' => Inertia::optional(function () use ($block) {
                $paginator = $this->getTransactions($block);

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->noResultsMessage($paginator->count()),
                ];
            }),
        ], [
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

        $spenderAddresses = $transactions
            ->map(fn ($t) => TransactionDTO::spenderAddress(new TransactionViewModel($t)))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $spenderWallets = count($spenderAddresses) > 0
            ? Wallet::whereIn('address', $spenderAddresses)->get()->keyBy('address')
            : collect();

        return (new LengthAwarePaginator($transactions, $transactionCount, $this->perPage(), $this->page(), [
            'path'     => route('block', $block),
            'pageName' => 'page',
        ]))->through(fn ($transaction) => TransactionDTO::fromModel($transaction, null, $spenderWallets));
    }
}
