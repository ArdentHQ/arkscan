<?php

declare(strict_types=1);

namespace App\Http\Livewire\Migration;

use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Transactions extends Component
{
    use HasPagination;

    /** @var mixed */
    protected $listeners = ['currencyChanged' => '$refresh'];

    public function render(): View
    {
        return view('livewire.migration.transactions', [
            'transactions' => ViewModelFactory::paginate(Transaction::migrated()->paginate(10)),
        ]);
    }
}
