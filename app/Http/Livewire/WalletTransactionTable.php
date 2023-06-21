<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Enums\CoreTransactionTypeEnum;
use App\Enums\TransactionTypeGroupEnum;
use App\Facades\Wallets;
use App\Http\Livewire\Concerns\DeferLoading;
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
final class WalletTransactionTable extends Component
{
    use DeferLoading;
    use HasTablePagination;

    public const PER_PAGE = 10;

    public string $address;

    public ?string $publicKey = null;

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

    public function queryString(): array
    {
        return [
            'outgoing'      => ['except' => true],
            'incoming'      => ['except' => true],
            'transfers'     => ['except' => true],
            'votes'         => ['except' => true],
            'multipayments' => ['except' => true],
            'others'        => ['except' => true],
        ];
    }

    public function mount(WalletViewModel $wallet, bool $deferLoading = true): void
    {
        $this->address   = $wallet->address();
        $this->publicKey = $wallet->publicKey();

        foreach ($this->filter as &$filter) {
            if (in_array($filter, ['1', 'true', true])) {
                $filter = true;
            } else if (in_array($filter, ['0', 'false', false])) {
                $filter = false;
            }
        }

        if (! $deferLoading) {
            $this->setIsReady();
        }
    }

    public function render(): View
    {
        return view('livewire.wallet-transaction-table', [
            'wallet'        => ViewModelFactory::make(Wallets::findByAddress($this->address)),
            'transactions'  => ViewModelFactory::paginate($this->transactions),
        ]);
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

    public function getTransactionsProperty(): LengthAwarePaginator
    {
        $emptyResults = new LengthAwarePaginator([], 0, $this->perPage);
        if (! $this->isReady) {
            return $emptyResults;
        }

        if (! $this->hasAddressingFilters()) {
            return $emptyResults;
        }

        if (! $this->hasTransactionTypeFilters()) {
            return $emptyResults;
        }

        return $this->getTransactionsQuery()
            ->withScope(OrderByTimestampScope::class)
            ->paginate($this->perPage);
    }

    public function state(): array
    {
        return [
            'address'   => $this->address,
            'publicKey' => $this->publicKey,
        ];
    }

    public function __get($property): mixed
    {
        if (isset($this->filter[$property])) {
            return $this->filter[$property];
        }

        return parent::__get($property);
    }

    public function __set($property, $value): void
    {
        if (isset($this->filter[$property])) {
            $this->filter[$property] = $value;
        }
    }

    private function hasAddressingFilters(): bool
    {
        if ($this->filter['incoming'] === true) {
            return true;
        }

        return $this->filter['outgoing'] === true;
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

        return $this->filter['others'] === true;
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
