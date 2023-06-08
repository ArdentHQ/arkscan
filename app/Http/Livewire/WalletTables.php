<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

final class WalletTables extends Component
{
    use HasPagination;

    public array $state = [
        'address'   => null,
        'publicKey' => null,
        'isCold'    => null,
        'type'      => 'all',
        'view'      => 'transactions',
    ];

    /** @var mixed */
    protected $listeners = [
        'currencyChanged' => '$refresh',
    ];

    public function mount(string $address, bool $isCold, ?string $publicKey): void
    {
        $this->state['address']   = $address;
        $this->state['publicKey'] = $publicKey;
        $this->state['isCold']    = $isCold;
    }

    public function updatedState(): void
    {
        $this->gotoPage(1);
    }

    public function render(): View
    {
        // if ($this->state['view'] === 'blocks') {
        //     $items         = $this->getReceivedQuery()->withScope(OrderByTimestampScope::class)->paginate();
        // } elseif ($this->state['view'] === 'voters') {
        //     $items         = $this->getSentQuery()->withScope(OrderByTimestampScope::class)->paginate();
        // } else {
            $items         = $this->getTransactionsQuery()->withScope(OrderByTimestampScope::class)->paginate();
        // }

        return view('livewire.wallet-tables', [
            'wallet'        => ViewModelFactory::make(Wallets::findByAddress($this->state['address'])),
            'transactions'  => ViewModelFactory::paginate($items),
        ]);
    }

    private function getTransactionsQuery(): Builder
    {
        $query = Transaction::query();

        $query->where(function ($query): void {
            $query->where('sender_public_key', $this->state['publicKey']);
        });

        $query->orWhere(function ($query): void {
            $query->where('recipient_id', $this->state['address']);
        });

        $query->orWhere(function ($query): void {
            $query->whereJsonContains('asset->payments', [['recipientId' => $this->state['address']]]);
        });

        return $query;
    }
}
