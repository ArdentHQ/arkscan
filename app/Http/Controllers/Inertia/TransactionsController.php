<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Transaction as TransactionDTO;
use App\Http\Controllers\Inertia\Concerns\WithFilters;
use App\Http\Controllers\Inertia\Concerns\WithPagination;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Scopes\OrderByTransactionIndexScope;
use App\Models\Transaction;
use App\Services\BigNumber;
use App\Services\Cache\StatisticsCache;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

final class TransactionsController
{
    use WithPagination;
    use WithFilters;

    public const FILTERS = [
        'transfers'           => true,
        'multipayments'       => true,
        'votes'               => true,
        'validator'           => true,
        'username'            => true,
        'contract_deployment' => true,
        'others'              => true,
    ];

    public function __invoke(): Response
    {
        $data = (new StatisticsCache())->getTransactionData();

        return Inertia::render('Transactions/Transactions', [
            'transactionCount' => $data['transaction_count'],
            'volume'           => BigNumber::new($data['volume'])->toFloat(),
            'totalFees'        => BigNumber::new($data['total_fees'])->toFloat(),
            'averageFee'       => BigNumber::new($data['average_fee'])->toFloat(),
            'transactions'     => Inertia::optional(function () {
                $paginator = $this->getTransactions();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getNoResultsMessageProperty($paginator->count()),
                ];
            }),
        ]);
    }

    public function getNoResultsMessageProperty(int $count): null|string
    {
        if (! $this->hasFilters()) {
            return trans('tables.transactions.no_results.no_filters');
        }

        return $count === 0
            ? (string) trans('tables.transactions.no_results.no_results')
            : null;
    }

    public function getTransactions(): LengthAwarePaginator
    {
        $emptyResults = new LengthAwarePaginator([], 0, $this->perPage('transactions'), $this->page(), [
            'pageName' => 'page',
        ]);

        if (! $this->hasFilters()) {
            return $emptyResults;
        }

        return Transaction::withTypeFilter(self::FILTERS)
            ->withScope(OrderByTimestampScope::class)
            ->withScope(OrderByTransactionIndexScope::class)
            ->with('votedFor')
            ->paginate($this->perPage('transactions'))
            ->through(fn (Transaction $transaction) => TransactionDTO::fromModel($transaction));
    }

    private function hasFilters(): bool
    {
        return collect(self::FILTERS)->some(fn ($_, $filterName) => $this->hasFilter($filterName, self::FILTERS[$filterName]));
    }
}
