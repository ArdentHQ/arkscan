<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Transaction as TransactionDTO;
use App\DTO\Inertia\Wallet as WalletDTO;
use App\Models\Scopes\HasMultiPaymentRecipientScope;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Scopes\OrderByTransactionIndexScope;
use App\Models\Transaction;
use App\Models\Wallet;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

final class WalletController
{
    // TODO: implement filters - https://app.clickup.com/t/86dy1up1c
    private array $filters = [
        'transactions' => [
            'outgoing'           => true,
            'incoming'           => true,
            'transfers'          => true,
            'multipayments'      => true,
            'votes'              => true,
            'validator'          => true,
            'username'           => true,
            'contract_deployment' => true,
            'others'             => true,
        ],
    ];

    public function __invoke(Wallet $wallet): Response
    {
        return Inertia::render('Wallet', [
            'wallet'       => WalletDTO::fromModel($wallet),

            'transactions' => Inertia::optional(function () use ($wallet) {
                $paginator = $this->getTransactions($wallet);

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getTransactionsNoResultsMessageProperty($paginator->count()),
                ];
            }),
        ]);
    }

    public function getTransactions(Wallet $wallet): AbstractPaginator
    {
        // TODO: implement filters - https://app.clickup.com/t/86dy1up1c
        // @codeCoverageIgnoreStart
        $emptyResults = (new LengthAwarePaginator([], 0, $this->perPage(), $this->page()));
        if (! $this->hasAddressingFilters()) {
            return $emptyResults;
        }

        if (! $this->hasTransactionTypeFilters()) {
            return $emptyResults;
        }
        // @codeCoverageIgnoreEnd

        return $this->getTransactionsQuery($wallet)
            ->withScope(OrderByTimestampScope::class)
            ->withScope(OrderByTransactionIndexScope::class)
            ->paginate($this->perPage(), page: $this->page())
            ->through(fn (Transaction $transaction) => TransactionDTO::fromModel($transaction, $wallet->address));
    }

    private function page(): int
    {
        return (int) request()->get('page', 1);
    }

    private function perPage(): int
    {
        return (int) request()->get('per-page', 25);
    }

    private function getTransactionsQuery(Wallet $wallet): Builder
    {
        return Transaction::query()
            ->withTypeFilter($this->filters['transactions'])
            ->with('votedFor')
            ->where(fn ($query) => $query->when($this->filters['transactions']['outgoing'], fn ($query) => $query->where('sender_public_key', $wallet->public_key)))
            ->orWhere(fn ($query) => $query->when($this->filters['transactions']['incoming'], fn ($query) => $query->where('to', $wallet->address)))
            ->orWhere(function ($query) use ($wallet) {
                $query->when($this->filters['transactions']['multipayments'], fn ($query) => $query->withScope(HasMultiPaymentRecipientScope::class, $wallet->address));
            });
    }

    /**
     * TODO: implement filters - https://app.clickup.com/t/86dy1up1c
     *
     * @codeCoverageIgnore
     */
    private function hasAddressingFilters(): bool
    {
        if ($this->filters['transactions']['incoming'] === true) {
            return true;
        }

        return $this->filters['transactions']['outgoing'] === true;
    }

    /**
     * TODO: implement filters - https://app.clickup.com/t/86dy1up1c
     *
     * @codeCoverageIgnore
     */
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

    private function getTransactionsNoResultsMessageProperty(int $count): null|string
    {
        // TODO: implement filters - https://app.clickup.com/t/86dy1up1c
        // @codeCoverageIgnoreStart
        if (! $this->hasAddressingFilters() && ! $this->hasTransactionTypeFilters()) {
            return trans('tables.transactions.no_results.no_filters');
        }

        if (! $this->hasAddressingFilters()) {
            return trans('tables.transactions.no_results.no_addressing_filters');
        }
        // @codeCoverageIgnoreEnd

        if ($count === 0) {
            return trans('tables.transactions.no_results.no_results');
        }

        return null;
    }
}
