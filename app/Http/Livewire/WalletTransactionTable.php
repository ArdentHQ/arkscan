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

        if ($this->state['type'] !== 'all') {
            $query = $query->withScope($this->scopes[$this->state['type']]);
        }

        return view('livewire.wallet-transaction-table', [
            'transactions'  => ViewModelFactory::paginate($query->latestByTimestamp()->paginate()),
            'countReceived' => $this->getReceivedQuery()->count(),
            'countSent'     => $this->getSentQuery()->count(),
        ]);
    }

    private function getAllQuery(): Builder
    {
        return Transaction::query()
            ->where('sender_public_key', $this->state['publicKey'])
            ->orWhere('recipient_id', $this->state['address'])
            ->orWhereJsonContains('asset->payments', [['recipientId' => $this->state['address']]]);
    }

    private function getReceivedQuery(): Builder
    {
        return Transaction::query()
            ->where('recipient_id', $this->state['address'])
            ->orWhereJsonContains('asset->payments', [['recipientId' => $this->state['address']]]);
    }

    private function getSentQuery(): Builder
    {
        return Transaction::where('sender_public_key', $this->state['publicKey']);
    }
}
