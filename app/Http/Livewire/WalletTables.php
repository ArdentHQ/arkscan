<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

final class WalletTables extends Component
{
    use HasTablePagination;

    public const PER_PAGE = 10;

    public string $address;

    public ?string $publicKey = null;

    public bool $isCold = false;

    public array $state = [
        'view' => 'transactions',
    ];

    /** @var mixed */
    protected $listeners = [
        'currencyChanged' => '$refresh',
    ];

    public function mount(WalletViewModel $wallet): void
    {
        $this->address   = $wallet->address();
        $this->publicKey = $wallet->publicKey();
        $this->isCold    = $wallet->isCold();
    }

    public function updatedState(): void
    {
        $this->gotoPage(1);
    }

    public function render(): View
    {
        // TODO: add block and voter table handling
        $items = $this->getTransactionsQuery()->withScope(OrderByTimestampScope::class)->paginate($this->perPage);

        return view('livewire.wallet-tables', [
            'wallet'        => ViewModelFactory::make(Wallets::findByAddress($this->address)),
            'transactions'  => ViewModelFactory::paginate($items),
        ]);
    }

    public function state(): array
    {
        return [
            ...$this->state,
            'address'   => $this->address,
            'publicKey' => $this->publicKey,
            'isCold'    => $this->isCold,
        ];
    }

    private function getTransactionsQuery(): Builder
    {
        $query = Transaction::query();

        $query->where(function ($query): void {
            $query->where('sender_public_key', $this->publicKey);
        });

        $query->orWhere(function ($query): void {
            $query->where('recipient_id', $this->address);
        });

        $query->orWhere(function ($query): void {
            $query->whereJsonContains('asset->payments', [['recipientId' => $this->address]]);
        });

        return $query;
    }
}
