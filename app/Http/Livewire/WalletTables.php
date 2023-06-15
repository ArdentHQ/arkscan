<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\CoreTransactionTypeEnum;
use App\Facades\Wallets;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use ARKEcosystem\Foundation\UserInterface\Http\Livewire\Concerns\HasPagination;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;

/** @property bool $isAllSelected */
final class WalletTables extends Component
{
    use HasPagination;

    public string $address;

    public ?string $publicKey = null;

    public bool $isCold = false;

    public array $state = [
        'view' => 'transactions',
    ];

    public array $filter = [
        'outgoing'      => false,
        'incoming'      => false,
        'transfers'     => false,
        'votes'         => false,
        'multipayments' => false,
        'others'        => false,
    ];

    public bool $selectAllFilters = false;

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
        $items = $this->getTransactionsQuery()->withScope(OrderByTimestampScope::class)->paginate();

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

    public function getIsAllSelectedProperty(): bool
    {
        return ! collect($this->filter)->contains(false);
    }

    public function updatedSelectAllFilters(bool $value): void
    {
        foreach ($this->filter as &$filter) {
            $filter = $value;
        }
    }

    public function updatedFilter(): void
    {
        $this->selectAllFilters = $this->isAllSelected;
    }

    private function getTransactionsQuery(): Builder
    {
        return Transaction::query()
            ->where(function ($query) {
                $query->when($this->showTransferTransactions() === true, fn ($query) => $query->where('type', CoreTransactionTypeEnum::TRANSFER))
                    ->when($this->showVoteTransactions() === true, fn ($query) => $query->where('type', CoreTransactionTypeEnum::VOTE))
                    ->when($this->showMultipaymentTransactions() === true, fn ($query) => $query->where('type', CoreTransactionTypeEnum::MULTI_PAYMENT));
                // ->when($this->showOtherTransactions() === true, fn ($query) => $query->where('type', CoreTransactionTypeEnum::MULTI_PAYMENT))
            })
            ->where(function ($query) {
                $query->where(fn ($query) => $query->when($this->showOutgoing(), fn ($query) => $query->where('sender_public_key', $this->publicKey)))
                    ->orWhere(fn ($query) => $query->when($this->showIncoming(), fn ($query) => $query->where('recipient_id', $this->address)))
                    ->orWhere(fn ($query) => $query->when($this->showIncoming(), fn ($query) => $query->whereJsonContains('asset->payments', [['recipientId' => $this->address]])));
            });
    }

    private function showOutgoing(): bool
    {
        if ($this->filter['outgoing']) {
            return true;
        }

        if ($this->filter['incoming']) {
            return false;
        }

        return true;
    }

    private function showIncoming(): bool
    {
        if ($this->filter['incoming']) {
            return true;
        }

        if ($this->filter['outgoing']) {
            return false;
        }

        return true;
    }

    private function showTransferTransactions(): ?bool
    {
        if ($this->filter['transfers']) {
            return true;
        }

        if ($this->filter['votes']) {
            return false;
        }

        if ($this->filter['multipayments']) {
            return false;
        }

        if ($this->filter['others']) {
            return false;
        }

        return null;
    }

    private function showVoteTransactions(): ?bool
    {
        if ($this->filter['votes']) {
            return true;
        }

        if ($this->filter['transfers']) {
            return false;
        }

        if ($this->filter['multipayments']) {
            return false;
        }

        if ($this->filter['others']) {
            return false;
        }

        return null;
    }

    private function showMultipaymentTransactions(): ?bool
    {
        if ($this->filter['multipayments']) {
            return true;
        }

        if ($this->filter['transfers']) {
            return false;
        }

        if ($this->filter['votes']) {
            return false;
        }

        if ($this->filter['others']) {
            return false;
        }

        return null;
    }

    private function showOtherTransactions(): ?bool
    {
        if ($this->filter['others']) {
            return true;
        }

        if ($this->filter['transfers']) {
            return false;
        }

        if ($this->filter['votes']) {
            return false;
        }

        if ($this->filter['multipayments']) {
            return false;
        }

        return null;
    }
}
