<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

final class WalletTransactionTable extends Component
{
    use HasPagination;

    public array $state = [
        'address'   => null,
        'publicKey' => null,
        'isCold'    => null,
        'type'      => 'all',
        'direction' => 'all',
    ];

    /** @phpstan-ignore-next-line */
    protected $listeners = [
        'filterTransactionsByDirection',
        'currencyChanged' => '$refresh',
    ];

    public function mount(string $address, bool $isCold, ?string $publicKey): void
    {
        $this->state['address']   = $address;
        $this->state['publicKey'] = $publicKey;
        $this->state['isCold']    = $isCold;
    }

    public function filterTransactionsByDirection(string $value): void
    {
        $this->state['direction'] = $value;
    }

    public function updatedState(): void
    {
        $this->gotoPage(1);
    }

    public function render(): View
    {
        if ($this->state['direction'] === 'received') {
            $items         = $this->getReceivedQuery()->withScope(OrderByTimestampScope::class)->paginate();
            $receivedCount = $items->total();
            $sentCount     = $this->getSentQuery()->count();
        } elseif ($this->state['direction'] === 'sent') {
            $items         = $this->getSentQuery()->withScope(OrderByTimestampScope::class)->paginate();
            $receivedCount = $this->getReceivedQuery()->count();
            $sentCount     = $items->total();
        } else {
            $items         = $this->getAllQuery()->withScope(OrderByTimestampScope::class)->paginate();
            $receivedCount = $this->getReceivedQuery()->count();
            $sentCount     = $this->getSentQuery()->count();
        }

        return view('livewire.wallet-transaction-table', [
            'wallet'        => ViewModelFactory::make(Wallets::findByAddress($this->state['address'])),
            'transactions'  => ViewModelFactory::paginate($items),
            'countReceived' => $receivedCount,
            'countSent'     => $sentCount,
        ]);
    }

    private function getAllQuery(): Builder
    {
        $query = Transaction::query();

        $query->where(function ($query): void {
            $query->where('sender_public_key', $this->state['publicKey']);

            $this->applyTypeScope($query);
        });

        $query->orWhere(function ($query): void {
            $query->where('recipient_id', $this->state['address']);

            $this->applyTypeScope($query);
        });

        $query->orWhere(function ($query): void {
            $query->whereJsonContains('asset->payments', [['recipientId' => $this->state['address']]]);

            $this->applyTypeScope($query);
        });

        return $query;
    }

    private function getReceivedQuery(): Builder
    {
        $query = Transaction::query();

        $query->where(function ($query): void {
            $query->where('recipient_id', $this->state['address']);

            $this->applyTypeScope($query);
        });

        $query->orWhere(function ($query): void {
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
            return $query->withScope(Transaction::TYPE_SCOPES[$this->state['type']]);
        }

        return $query;
    }
}
