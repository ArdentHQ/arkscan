<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Block as BlockDTO;
use App\DTO\Inertia\Transaction as TransactionDTO;
use App\DTO\Inertia\Wallet as WalletDTO;
use App\Http\Controllers\Inertia\Concerns\WithPagination;
use App\Models\Block;
use App\Models\Transaction;
use App\Models\Wallet;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

final class BookmarksController
{
    use WithPagination;

    public function __invoke(): Response
    {
        return Inertia::renderWithMeta('Bookmarks/Index', 'bookmarks', [
            'addresses'    => Inertia::optional(fn () => $this->getAddresses()),
            'transactions' => Inertia::optional(fn () => $this->getTransactions()),
            'blocks'       => Inertia::optional(fn () => $this->getBlocks()),
        ]);
    }

    private function getAddresses(): array
    {
        $ids = (array) request()->query('addresses', []);

        if ($ids === []) {
            return $this->emptyPaginator(trans('tables.bookmarks.addresses.no_results'));
        }

        $paginator = Wallet::whereIn('address', $ids)
            ->paginate($this->perPage(), page: $this->page())
            ->through(fn (Wallet $wallet) => WalletDTO::fromModel($wallet));

        return $this->formatPaginator($paginator, trans('tables.bookmarks.addresses.no_results'));
    }

    private function getTransactions(): array
    {
        $ids = (array) request()->query('transactions', []);

        if ($ids === []) {
            return $this->emptyPaginator(trans('tables.bookmarks.transactions.no_results'));
        }

        $paginator = Transaction::whereIn('hash', $ids)
            ->paginate($this->perPage(), page: $this->page())
            ->through(fn (Transaction $transaction) => TransactionDTO::fromModel($transaction));

        return $this->formatPaginator($paginator, trans('tables.bookmarks.transactions.no_results'));
    }

    private function getBlocks(): array
    {
        $ids = (array) request()->query('blocks', []);

        if ($ids === []) {
            return $this->emptyPaginator(trans('tables.bookmarks.blocks.no_results'));
        }

        $paginator = Block::whereIn('hash', $ids)
            ->paginate($this->perPage(), page: $this->page())
            ->through(fn (Block $block) => BlockDTO::fromModel($block));

        return $this->formatPaginator($paginator, trans('tables.bookmarks.blocks.no_results'));
    }

    private function formatPaginator(LengthAwarePaginator $paginator, string $noResultsMessage): array
    {
        return [
            ...$paginator->toArray(),
            'meta'             => UI::getPaginationData($paginator),
            'noResultsMessage' => $paginator->count() === 0 ? $noResultsMessage : null,
        ];
    }

    private function emptyPaginator(string $noResultsMessage): array
    {
        $paginator = new LengthAwarePaginator([], 0, $this->perPage(), $this->page(), [
            'pageName' => 'page',
        ]);

        return [
            ...$paginator->toArray(),
            'meta'             => UI::getPaginationData($paginator),
            'noResultsMessage' => $noResultsMessage,
        ];
    }
}
