<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

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
use App\DTO\Inertia\Transaction as TransactionDTO;

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
        if ($this->hasFilter('transfers', self::FILTERS['transfers']) === true) {
            return true;
        }

        if ($this->hasFilter('multipayments', self::FILTERS['multipayments']) === true) {
            return true;
        }

        if ($this->hasFilter('votes', self::FILTERS['votes']) === true) {
            return true;
        }

        if ($this->hasFilter('validator', self::FILTERS['validator']) === true) {
            return true;
        }

        if ($this->hasFilter('username', self::FILTERS['username']) === true) {
            return true;
        }

        if ($this->hasFilter('contract_deployment', self::FILTERS['contract_deployment']) === true) {
            return true;
        }

        return $this->hasFilter('others', self::FILTERS['others']) === true;
    }
}
