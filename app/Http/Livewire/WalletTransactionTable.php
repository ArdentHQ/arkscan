<?php

declare(strict_types=1);

namespace App\Http\Livewire;

use App\Facades\Wallets;
use App\Http\Livewire\Abstracts\TabbedTableComponent;
use App\Http\Livewire\Concerns\DeferLoading;
use App\Http\Livewire\Concerns\HasTableFilter;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Transaction;
use App\ViewModels\ViewModelFactory;
use App\ViewModels\WalletViewModel;
use ArkEcosystem\Crypto\Enums\Types;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * @property bool $isAllSelected
 * @property LengthAwarePaginator $transactions
 * */
final class WalletTransactionTable extends TabbedTableComponent
{
    use DeferLoading;
    use HasTableFilter;

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

    /** @var mixed */
    protected $listeners = [
        'setTransactionsReady' => 'setIsReady',
        'currencyChanged'      => '$refresh',
    ];

    public function queryString(): array
    {
        return [
            'filter.outgoing'      => ['as' => 'outgoing', 'except' => true],
            'filter.incoming'      => ['as' => 'incoming', 'except' => true],
            'filter.transfers'     => ['as' => 'transfers', 'except' => true],
            'filter.votes'         => ['as' => 'votes', 'except' => true],
            'filter.multipayments' => ['as' => 'multipayments', 'except' => true],
            'filter.others'        => ['as' => 'others', 'except' => true],
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

        if ($this->filter['multipayments'] === true) {
            return true;
        }

        return $this->filter['others'] === true;
    }

    private function getTransactionsQuery(): Builder
    {
        return Transaction::query()
            ->withTypeFilter($this->filter)
            ->where(function ($query) {
                $query->where(fn ($query) => $query->when($this->filter['outgoing'], fn ($query) => $query->where('sender_public_key', $this->publicKey)))
                    ->orWhere(fn ($query) => $query->when($this->filter['incoming'], fn ($query) => $query->where('recipient_id', $this->address)))
                    ->orWhere(fn ($query) => $query->when($this->filter['incoming'], fn ($query) => $query
                        ->where('type', Types::MULTI_PAYMENT)
                        ->whereJsonContains('asset->payments', [['recipientId' => $this->address]])));
            });
    }
}
