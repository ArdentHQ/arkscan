<?php

declare(strict_types=1);

namespace App\Http\Livewire\Wallet\Concerns;

use App\Models\Scopes\HasMultiPaymentRecipientScope;
use App\Models\Scopes\MultiPaymentScope;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Scopes\OrderByTransactionIndexScope;
use App\Models\Transaction;
use App\ViewModels\WalletViewModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Attributes\On;

/**
 * @property bool $isAllSelected
 * @property LengthAwarePaginator $transactions
 * */
trait TransactionsTab
{
    public bool $transactionsIsReady = false;

    public string $address;

    public ?string $publicKey = null;

    public function getListenersTransactionsTab(): array
    {
        return [
            'reloadTransactions' => '$refresh',
        ];
    }

    public function queryStringTransactionsTab(): array
    {
        return [
            'paginators.transactions'                  => ['except' => 1, 'as' => 'page', 'history' => true],
            'paginatorsPerPage.transactions'           => ['except' => self::defaultPerPage('TRANSACTIONS'), 'as' => 'per-page', 'history' => true],
            'filters.transactions.outgoing'            => ['as' => 'outgoing', 'except' => true],
            'filters.transactions.incoming'            => ['as' => 'incoming', 'except' => true],
            'filters.transactions.transfers'           => ['as' => 'transfers', 'except' => true],
            'filters.transactions.multipayments'       => ['as' => 'multipayments', 'except' => true],
            'filters.transactions.votes'               => ['as' => 'votes', 'except' => true],
            'filters.transactions.validator'           => ['as' => 'validator', 'except' => true],
            'filters.transactions.username'            => ['as' => 'username', 'except' => true],
            'filters.transactions.contract_deployment' => ['as' => 'contract-deployment', 'except' => true],
            'filters.transactions.others'              => ['as' => 'others', 'except' => true],
        ];
    }

    // We're keeping it here as TabbedComponent has its own mount method
    // and we can't override it with arguments.
    public function mountTransactionsTab(WalletViewModel $wallet, bool $deferLoading = true): void
    {
        $this->address   = $wallet->address();
        $this->publicKey = $wallet->publicKey();

        if (! $deferLoading) {
            $this->setTransactionsReady();
        }
    }

    public function getTransactionsNoResultsMessageProperty(): null|string
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
        $emptyResults = new LengthAwarePaginator([], 0, $this->getPerPage('transactions'), $this->getPage('transactions'));
        if (! $this->transactionsIsReady) {
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
            ->withScope(OrderByTransactionIndexScope::class)
            ->paginate($this->getPerPage('transactions'), page: $this->getPage('transactions'));
    }

    #[On('setTransactionsReady')]
    public function setTransactionsReady(): void
    {
        $this->transactionsIsReady = true;
    }

    private function hasAddressingFilters(): bool
    {
        if ($this->filters['transactions']['incoming'] === true) {
            return true;
        }

        return $this->filters['transactions']['outgoing'] === true;
    }

    private function hasTransactionTypeFilters(): bool
    {
        if ($this->filters['transactions']['transfers'] === true) {
            return true;
        }

        if ($this->filters['transactions']['multipayments'] === true) {
            return true;
        }

        if ($this->filters['transactions']['votes'] === true) {
            return true;
        }

        if ($this->filters['transactions']['validator'] === true) {
            return true;
        }

        if ($this->filters['transactions']['username'] === true) {
            return true;
        }

        if ($this->filters['transactions']['contract_deployment'] === true) {
            return true;
        }

        return $this->filters['transactions']['others'] === true;
    }

    private function getTransactionsQuery(): Builder
    {
        return Transaction::query()
            ->withTypeFilter($this->filters['transactions'])
            ->with('votedFor')
            ->where(function ($query) {
                $query->where(fn ($query) => $query->when($this->filters['transactions']['outgoing'], fn ($query) => $query->where('sender_public_key', $this->publicKey)))
                    ->orWhere(fn ($query) => $query->when($this->filters['transactions']['incoming'], fn ($query) => $query->where('to', $this->address)))
                    ->orWhere(function ($query) {
                        $query->when($this->filters['transactions']['multipayments'], function ($query) {
                            $query->withScope(HasMultiPaymentRecipientScope::class, $this->address);
                        });
                    });
                });
    }
}
