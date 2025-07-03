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
        'setTransactionsReady' => 'setIsReady',
        'currencyChanged'      => '$refresh',
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

        return Transaction::query()
            ->with('votedFor', 'unvotedFor')
            ->withScope(OrderByTimestampScope::class)
            ->paginate($this->perPage);
    }
}
