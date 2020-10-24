<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\ManagesTransactionTypeScopes;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use Illuminate\View\View;
use Livewire\Component;

final class LatestTransactionsTable extends Component
{
    use ManagesTransactionTypeScopes;

    public array $state = [
        'type' => 'all',
    ];

    /** @phpstan-ignore-next-line */
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

        return view('livewire.latest-transactions-table', [
            'transactions' => ViewModelFactory::collection(Transaction::latestByTimestamp()->take(15)->get()),
        ]);
    }
}
