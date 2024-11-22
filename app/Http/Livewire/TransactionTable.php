<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTableFilter;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Scopes\OrderByTimestampScope;
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

    public array $filter = [
        'transfers' => true,
        'votes'     => true,
        'others'    => true,
    ];

    /** @var mixed */
    protected $listeners = [
        'currencyChanged'                  => '$refresh',
        'echo:transactions,NewTransaction' => '$refresh',
    ];

    public function queryString(): array
    {
        return [
            'transfers' => ['except' => true],
            'votes'     => ['except' => true],
            'others'    => ['except' => true],
        ];
    }

    public function render(): View
    {
        return view('livewire.transaction-table', [
            'transactions'  => ViewModelFactory::paginate($this->transactions),
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

        return Transaction::withTypeFilter($this->filter)
            ->withScope(OrderByTimestampScope::class)
            ->paginate($this->perPage);
    }

    private function hasTransactionTypeFilters(): bool
    {
        if ($this->filter['transfers'] === true) {
            return true;
        }

        if ($this->filter['votes'] === true) {
            return true;
        }

        return $this->filter['others'] === true;
    }
}
