<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Facades\Wallets;
use App\Http\Livewire\Concerns\HasTablePagination;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use ArkEcosystem\Crypto\Enums\Types;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Component;

/** @property bool $isAllSelected */
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
        $items = null;
        if ($this->hasFilters()) {
            // TODO: add block and voter table handling
            $items = $this->getTransactionsQuery()->withScope(OrderByTimestampScope::class)->paginate($this->perPage);
        }

        if ($items === null) {
            $items = new LengthAwarePaginator([], 0, $this->perPage);
        }

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

        $this->setPage(1);
    }

    private function hasFilters(): bool
    {
        if ($this->filter['incoming'] === true) {
            return true;
        }

        return $this->filter['outgoing'];
    }

    private function getTransactionsQuery(): Builder
    {
        return Transaction::query()
            ->where(function ($query) {
                $query->where(fn ($query) => $query->when($this->filter['transfers'] === true, fn ($query) => $query->where('type', CoreTransactionTypeEnum::TRANSFER)))
                    ->orWhere(fn ($query) => $query->when($this->filter['votes'] === true, fn ($query) => $query->where('type', CoreTransactionTypeEnum::VOTE)))
                    ->orWhere(fn ($query) => $query->when($this->filter['multipayments'] === true, fn ($query) => $query->where('type', CoreTransactionTypeEnum::MULTI_PAYMENT)))
                    ->orWhere(fn ($query) => $query->when($this->filter['others'] === true, fn ($query) => $query
                        ->where('type_group', TransactionTypeGroupEnum::MAGISTRATE)
                        ->orWhere(fn ($query) =>
                            $query
                                ->where('type_group', TransactionTypeGroupEnum::CORE)
                                ->whereNotIn('type', [
                                    CoreTransactionTypeEnum::TRANSFER,
                                    CoreTransactionTypeEnum::VOTE,
                                    CoreTransactionTypeEnum::MULTI_PAYMENT,
                                ])
                        )));
            })
            ->where(function ($query) {
                $query->where(fn ($query) => $query->when($this->filter['outgoing'], fn ($query) => $query->where('sender_public_key', $this->publicKey)))
                    ->orWhere(fn ($query) => $query->when($this->filter['incoming'], fn ($query) => $query->where('recipient_id', $this->address)))
                    ->orWhere(fn ($query) => $query->when($this->filter['incoming'], fn ($query) => $query
                        ->where('type', Types::MULTI_PAYMENT)
                        ->whereJsonContains('asset->payments', [['recipientId' => $this->address]])));
            });
    }
}
