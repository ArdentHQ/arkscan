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

/**
 * @property bool $isAllSelected
 * @property LengthAwarePaginator $transactions
 * */
final class WalletTables extends Component
{
    use HasTablePagination;

    public const PER_PAGE = 10;

    public string $address;

    public ?string $publicKey = null;

    public bool $isCold = false;

    public string $view = 'transactions';

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

    public function getQueryString(): array
    {
        return [
            'perPage' => ['except' => $this->getDefault()],
            'view'    => ['except' => 'transactions'],
        ];
    }

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
        return view('livewire.wallet-tables', [
            'wallet'        => ViewModelFactory::make(Wallets::findByAddress($this->address)),
            'transactions'  => ViewModelFactory::paginate($this->transactions),
        ]);
    }

    public function state(): array
    {
        return [
            'view'      => $this->view,
            'address'   => $this->address,
            'publicKey' => $this->publicKey,
            'isCold'    => $this->isCold,
        ];
    }

    public function getIsAllSelectedProperty(): bool
    {
        return ! collect($this->filter)->contains(false);
    }

    public function getNoResultsMessageProperty(): null|string
    {
        if (! $this->hasAddressingFilters() && ! $this->hasTransactionTypeFilters()) {
            return trans('tables.transactions.no_results.no_filters');
        }

        if (! $this->hasAddressingFilters()) {
            return trans('tables.transactions.no_results.no_addressing_filters');
        }

        if ($this->transactions->total() === 0) {
            return trans('tables.transactions.no_results.no_results');
        }

        return null;
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

    // TODO: add block and voter table handling
    public function getTransactionsProperty(): LengthAwarePaginator
    {
        if ($this->hasAddressingFilters() && $this->hasTransactionTypeFilters()) {
            return $this->getTransactionsQuery()
                ->withScope(OrderByTimestampScope::class)
                ->paginate($this->perPage);
        }

        return new LengthAwarePaginator([], 0, $this->perPage);
    }

    private function hasAddressingFilters(): bool
    {
        if ($this->filter['incoming'] === true) {
            return true;
        }

        return $this->filter['outgoing'];
    }

    private function hasTransactionTypeFilters(): bool
    {
        if ($this->filter['transfers'] === true) {
            return true;
        }

        if ($this->filter['votes'] === true) {
            return true;
        }

        if ($this->filter['multipayments'] === true) {
            return true;
        }

        return $this->filter['others'];
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
                        ->orWhere(
                            fn ($query) => $query
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
