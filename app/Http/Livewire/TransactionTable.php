<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use Livewire\Component;

final class TransactionTable extends Component
{
    public function render()
    {
        return view('livewire.transaction-table', [
            'transactions' => ViewModelFactory::paginate(Transaction::latestByTimestamp()->paginate()),
        ]);
    }
}
