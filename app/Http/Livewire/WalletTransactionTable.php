<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\Http\Livewire\Abstracts\TabbedTableComponent;
use App\Http\Livewire\Concerns\HasTableFilter;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property bool $isAllSelected
 * @property LengthAwarePaginator $transactions
 * */
final class WalletTransactionTable extends TabbedTableComponent
{
    use HasTableFilter;

    public string $address;

    public ?string $publicKey = null;

    public array $filter = [
        'outgoing'               => true,
        'incoming'               => true,
        'transfers'              => true,
        'votes'                  => true,
        'unvotes'                => true,
        'validator_registration' => true,
        'validator_resignation'  => true,
        'others'                 => true,
    ];

    /** @var mixed */
    protected $listeners = [
        'setTransactionsReady' => 'setIsReady',
        'currencyChanged'      => '$refresh',
        'reloadTransactions'   => '$refresh',
    ];

    public function queryString(): array
    {
        return [
            'filter.outgoing'               => ['as' => 'outgoing', 'except' => true],
            'filter.incoming'               => ['as' => 'incoming', 'except' => true],
            'filter.transfers'              => ['as' => 'transfers', 'except' => true],
            'filter.votes'                  => ['as' => 'votes', 'except' => true],
            'filter.unvotes'                => ['as' => 'unvotes', 'except' => true],
            'filter.validator_registration' => ['as' => 'validator-registration', 'except' => true],
            'filter.validator_resignation'  => ['as' => 'validator-resignation', 'except' => true],
            'filter.others'                 => ['as' => 'others', 'except' => true],
        ];
    }

    public function mount(WalletViewModel $wallet, bool $deferLoading = true): void
    {
        $this->address   = $wallet->address();
        $this->publicKey = $wallet->publicKey();

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
            ->withTypeFilter($this->filter)
            ->withScope(OrderByTimestampScope::class)
            ->paginate($this->perPage);
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

        if ($this->filter['unvotes'] === true) {
            return true;
        }

        if ($this->filter['validator_registration'] === true) {
            return true;
        }

        if ($this->filter['validator_resignation'] === true) {
            return true;
        }

        return $this->filter['others'] === true;
    }

    private function getTransactionsQuery(): Builder
    {
        return Transaction::query()
            ->where(function ($query) {
                $query->where(fn ($query) => $query->when($this->filter['outgoing'], fn ($query) => $query->where('sender_public_key', $this->publicKey)))
                    ->orWhere(fn ($query) => $query->when($this->filter['incoming'], fn ($query) => $query->where('recipient_address', $this->address)));
            });
    }
}
