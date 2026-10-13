<?php

declare(strict_types=1);

namespace App\Http\Livewire\Home;

use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Livewire\Component;

/**
 * @property LengthAwarePaginator $transactions
 * */
final class Transactions extends Component
{
    use DeferLoading;
    use HasTablePagination;

    /** @var mixed */
    protected $listeners = [
        'setTransactionsReady'             => 'setIsReady',
        'currencyChanged'                  => '$refresh',
        'echo:transactions,NewTransaction' => '$refresh',
    ];

    public function render(): View
    {
        return view('livewire.home.transactions', [
            'transactions'  => ViewModelFactory::paginate($this->transactions),
        ]);
    }

    public function getNoResultsMessageProperty(): null|string
    {
        if ($this->transactions->total() === 0) {
            return trans('tables.transactions.no_results.no_results');
        }

        return null;
    }

    public function getTransactionsProperty(): LengthAwarePaginator
    {
        if (! $this->isReady) {
            return new LengthAwarePaginator([], 0, $this->perPage);
        }

        $page = $this->page ?? 1;

        $total = Cache::remember('transactions_total_count', 60, fn () => Transaction::query()->count());

        $transactions = Transaction::query()
            ->withScope(OrderByTimestampScope::class)
            ->forPage($page, $this->perPage)
            ->get();

        return new LengthAwarePaginator($transactions, $total, $this->perPage, $page);
    }
}
