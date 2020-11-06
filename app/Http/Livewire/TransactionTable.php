<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\ManagesTransactionTypeScopes;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\View\View;
use Livewire\Component;

final class TransactionTable extends Component
{
    use HasPagination;
    use ManagesTransactionTypeScopes;

    public array $state = [
        'type' => 'all',
    ];

    /** @phpstan-ignore-next-line */
    protected $listeners = ['filterTransactionsByType'];

    public function filterTransactionsByType(string $value): void
    {
        $this->state['type'] = $value;

        $this->gotoPage(1);
    }

    public function render(): View
    {
        $query = Transaction::withScope(OrderByTimestampScope::class);

        if ($this->state['type'] !== 'all') {
            $scopeClass = $this->scopes[$this->state['type']];

            /* @var \Illuminate\Database\Eloquent\Model */
            $query = $query->withScope($scopeClass);
        }

        return view('livewire.transaction-table', [
            'transactions' => ViewModelFactory::paginate($query->paginate()),
        ]);
    }
}
