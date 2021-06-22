<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class TransactionTable extends Component
{
    use HasPagination;

    /** @phpstan-ignore-next-line */
    protected $listeners = ['currencyChanged' => '$refresh'];

    public array $state = [
        'type' => 'all',
    ];

    public function updatedStateType(): void
    {
        $this->gotoPage(1);
    }

    public function mount(): void
    {
        $this->state = array_merge([
            'type'        => 'all',
        ], request('state', []));
    }

    public function render(): View
    {
        $query = Transaction::withScope(OrderByTimestampScope::class);

        if ($this->state['type'] !== 'all') {
            $scopeClass = Transaction::TYPE_SCOPES[$this->state['type']];

            $query = $query->withScope($scopeClass);
        }

        return view('livewire.transaction-table', [
            'showTitle'    => true,
            'transactions' => ViewModelFactory::paginate($query->paginate()),
        ]);
    }
}
