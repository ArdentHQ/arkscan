<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTableFilter;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Scopes\OrderByTransactionIndexScope;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;

/**
 * @property bool $isAllSelected
 * @property LengthAwarePaginator $transactions
 * */
final class TransactionTable extends Component
{
    use DeferLoading;
    use HasTableFilter;
    use HasTablePagination;

    public const INITIAL_FILTERS = [
        'default' => [
            'transfers'           => true,
            'multipayments'       => true,
            'votes'               => true,
            'validator'           => true,
            'username'            => true,
            'contract_deployment' => true,
            'others'              => true,
        ],
    ];

    /** @var mixed */
    protected $listeners = [
        'currencyChanged'                  => '$refresh',
        'echo:transactions,NewTransaction' => '$refresh',
    ];

    public function queryString(): array
    {
        return [
            'filters.default.transfers'           => ['as' => 'transfers', 'except' => true],
            'filters.default.multipayments'       => ['as' => 'multipayments', 'except' => true],
            'filters.default.votes'               => ['as' => 'votes', 'except' => true],
            'filters.default.validator'           => ['as' => 'validator', 'except' => true],
            'filters.default.username'            => ['as' => 'username', 'except' => true],
            'filters.default.contract_deployment' => ['as' => 'contract-deployment', 'except' => true],
            'filters.default.others'              => ['as' => 'others', 'except' => true],
        ];
    }

    public function render(): View
    {
        return view('livewire.transaction-table', [
            'transactions' => ViewModelFactory::paginate($this->transactions),
        ]);
    }

    public function getNoResultsMessageProperty(): null|string
    {
        if (! $this->hasTransactionTypeFilters()) {
            return trans('tables.transactions.no_results.no_filters');
        }

        if ($this->transactions->total() === 0) {
            return trans('tables.transactions.no_results.no_results');
        }

        return null;
    }

    public function getTransactionsProperty(): LengthAwarePaginator
    {
        $emptyResults = new LengthAwarePaginator([], 0, $this->perPage);
        if (! $this->isReady) {
            return $emptyResults;
        }

        if (! $this->hasTransactionTypeFilters()) {
            return $emptyResults;
        }

        return Transaction::withTypeFilter($this->getFilters())
            ->withScope(OrderByTimestampScope::class)
            ->withScope(OrderByTransactionIndexScope::class)
            ->with('votedFor')
            ->paginate($this->perPage);
    }

    private function hasTransactionTypeFilters(): bool
    {
        if ($this->getFilter('transfers') === true) {
            return true;
        }

        if ($this->getFilter('multipayments') === true) {
            return true;
        }

        if ($this->getFilter('votes') === true) {
            return true;
        }

        if ($this->getFilter('validator') === true) {
            return true;
        }

        if ($this->getFilter('username') === true) {
            return true;
        }

        if ($this->getFilter('contract_deployment') === true) {
            return true;
        }

        return $this->getFilter('others') === true;
    }
}
