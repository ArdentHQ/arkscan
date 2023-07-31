<?php

namespace App\Http\Livewire\Filters;

use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasModal;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class WalletTransactions extends Component
{
    use HasModal;

    public array $filter = [
        'outgoing'      => true,
        'incoming'      => true,
        'transfers'     => true,
        'votes'         => true,
        'multipayments' => true,
        'others'        => true,
    ];

    public bool $selectAllFilters = true;

    /** @var mixed */
    protected $listeners = [
        'setTransactionsFilter',
        'setTransactionsFilterSelectAll',
    ];

    public function render(): View
    {
        return view('livewire.filters.wallet-transactions');
    }

    public function updatedSelectAllFilters(bool $value): void
    {
        $this->emitUp('setTransactionsFilterSelectAll', $value);
    }

    public function updatedFilter(bool $value, string $key): void
    {
        $this->emitUp('setTransactionsFilter', $key, $value);
    }

    public function setTransactionsFilter(string $filter, bool $value): void
    {
        $this->filter[$filter] = $value;
    }

    public function setTransactionsFilterSelectAll(bool $value): void
    {
        $this->selectAllFilters = $value;
    }
}
