<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Http\Livewire\Concerns\ManagesTransactionTypeScopes;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;
use Livewire\Component;

final class WalletTransactionTable extends Component
{
    use HasPagination;
    use ManagesTransactionTypeScopes;

    public array $state = [
        'address'   => null,
        'publicKey' => null,
        'type'      => 'all',
        'direction' => 'all',
    ];

    /** @phpstan-ignore-next-line */
    protected $listeners = [
        'filterTransactionsByDirection',
        'filterTransactionsByType',
    ];

    public function mount(string $address, string $publicKey): void
    {
        $this->state['address']   = $address;
        $this->state['publicKey'] = $publicKey;
    }

    public function filterTransactionsByDirection(string $value): void
    {
        $this->state['direction'] = $value;
    }

    public function filterTransactionsByType(string $value): void
    {
        $this->state['type'] = $value;
    }

    public function render(): View
    {
        if ($this->state['direction'] === 'received') {
            $query = $this->getReceivedQuery();
        } elseif ($this->state['direction'] === 'sent') {
            $query = $this->getSentQuery();
        } else {
            $query = $this->getAllQuery();
        }

        return view('livewire.wallet-transaction-table', [
            'transactions'  => ViewModelFactory::paginate($query->latestByTimestamp()->paginate()),
            'countReceived' => $this->getReceivedQuery()->count(),
            'countSent'     => $this->getSentQuery()->count(),
        ]);
    }

    private function getAllQuery(): Builder
    {
        $query = Transaction::query();

        $query->where(function ($query) {
            $query->where('sender_public_key', $this->state['publicKey']);

            $this->applyTypeScope($query);
        });

        $query->orWhere(function ($query) {
            $query->where('recipient_id', $this->state['address']);

            $this->applyTypeScope($query);
        });

        $query->orWhere(function ($query) {
            $query->whereJsonContains('asset->payments', [['recipientId' => $this->state['address']]]);

            $this->applyTypeScope($query);
        });

        return $query;
    }

    private function getReceivedQuery(): Builder
    {
        $query = Transaction::query();

        $query->where(function ($query) {
            $query->where('recipient_id', $this->state['address']);

            $this->applyTypeScope($query);
        });

        $query->orWhere(function ($query) {
            $query->whereJsonContains('asset->payments', [['recipientId' => $this->state['address']]]);

            $this->applyTypeScope($query);
        });

        return $query;
    }

    private function getSentQuery(): Builder
    {
        return $this->applyTypeScope(Transaction::where('sender_public_key', $this->state['publicKey']));
    }

    private function applyTypeScope(Builder $query): Builder
    {
        if ($this->state['type'] !== 'all') {
            return $query->withScope($this->scopes[$this->state['type']]);
        }

        return $query;
    }
}
