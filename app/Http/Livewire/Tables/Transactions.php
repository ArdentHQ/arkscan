<?php

declare(strict_types=1);

namespace App\Http\Livewire\Tables;

use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;

final class Transactions extends Component
{
    use HasPagination;

    protected LengthAwarePaginator $transactions;

    public function mount(Builder $transactions): void
    {
        $this->transactions = $transactions->paginate();
    }

    public function render(): View
    {
        return view('livewire.tables.transactions', [
            'transactions' => ViewModelFactory::paginate($this->transactions),
        ]);
    }
}
