<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\CanViewMore;
use App\Http\Livewire\Concerns\ManagesTransactionTypeScopes;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\View\View;
use Livewire\Component;

final class TransactionTable extends Component
{
    use CanViewMore;
    use HasPagination;
    use ManagesTransactionTypeScopes;

    public array $state = [
        'type' => 'all',
    ];

    protected $listeners = ['filterTransactionsByType'];

    public function filterTransactionsByType(string $value): void
    {
        $this->state['type'] = $value;
    }

    public function render(): View
    {
        if ($this->state['type'] !== 'all') {
            $scopeClass = $this->scopes[$this->state['type']];

            /* @var \Illuminate\Database\Eloquent\Model */
            Transaction::addGlobalScope(new $scopeClass());
        }

        return view('livewire.transaction-table', [
            'transactions' => ViewModelFactory::paginate(Transaction::latestByTimestamp()->paginate()),
        ]);
    }
}
