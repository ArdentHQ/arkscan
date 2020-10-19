<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use Livewire\Component;
use Livewire\WithPagination;

final class TransactionTable extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.transaction-table', [
            'transactions' => ViewModelFactory::paginate(Transaction::latestByTimestamp()->paginate()),
        ]);
    }
}
