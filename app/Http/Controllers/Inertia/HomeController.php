<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Transaction as TransactionDTO;
use App\Facades\Network;
use App\Http\Controllers\Inertia\Concerns\WithPagination;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

final class HomeController
{
    use WithPagination;

    public function __invoke(): Response
    {
        return Inertia::render('Home/Index', [
            'transactions' => Inertia::optional(function () {
                $paginator = $this->getTransactions();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getNoResultsMessageProperty($paginator->total()),
                ];
            }),
        ])
            ->withMeta('home', [
                'name' => Network::currency(),
            ]);
    }

    public function getNoResultsMessageProperty(int $total): ?string
    {
        return $total === 0
            ? (string) trans('tables.transactions.no_results.no_results')
            : null;
    }

    public function getTransactions(): LengthAwarePaginator
    {
        return Transaction::query()
            ->with('votedFor')
            ->withScope(OrderByTimestampScope::class)
            ->paginate($this->perPage('transactions'), page: $this->page())
            ->through(fn (Transaction $transaction) => TransactionDTO::fromModel($transaction));
    }
}
