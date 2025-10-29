<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Block as BlockDTO;
use App\DTO\Inertia\Transaction as TransactionDTO;
use App\DTO\Inertia\Wallet as WalletDTO;
use App\Models\Block;
use App\Models\Scopes\HasMultiPaymentRecipientScope;
use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Scopes\OrderByHeightScope;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Scopes\OrderByTransactionIndexScope;
use App\Models\Transaction;
use App\Models\Wallet;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;

final class WalletController
{
    public const FILTERS = [
        'transactions' => [
            'outgoing'            => true,
            'incoming'            => true,
            'transfers'           => true,
            'multipayments'       => true,
            'votes'               => true,
            'validator'           => true,
            'username'            => true,
            'contract_deployment' => true,
            'others'              => true,
        ],
    ];

    public function __invoke(Wallet $wallet): Response
    {
        return Inertia::render('Wallet/Wallet', [
            'wallet'       => WalletDTO::fromModel($wallet),
            'filters'      => self::FILTERS,

            'transactions' => Inertia::optional(function () use ($wallet) {
                $paginator = $this->getTransactions($wallet);

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getTransactionsNoResultsMessageProperty($paginator->count()),
                ];
            }),

            'blocks' => Inertia::optional(function () use ($wallet) {
                $paginator = $this->getBlocks($wallet);

                return [
                    ...$paginator->toArray(),

                    'meta' => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getValidatedBlocksNoResultsMessageProperty($paginator->count()),
                ];
            }),

            "voters" => Inertia::optional(function () use ($wallet) {
                $paginator = $this->getVoters($wallet);

                return [
                    ...$paginator->toArray(),

                    'meta' => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getVotersNoResultsMessageProperty($paginator->count()),
                ];
            }),
        ]);
    }

    public function getTransactions(Wallet $wallet): AbstractPaginator
    {
        $emptyResults = new LengthAwarePaginator([], 0, $this->perPage(), $this->page(), [
            'pageName' => 'page',
        ]);

        if (! $this->hasAddressingFilters()) {
            return $emptyResults;
        }

        if (! $this->hasTransactionTypeFilters()) {
            return $emptyResults;
        }

        return $this->getTransactionsQuery($wallet)
            ->withScope(OrderByTimestampScope::class)
            ->withScope(OrderByTransactionIndexScope::class)
            ->paginate($this->perPage(), page: $this->page())
            ->through(fn (Transaction $transaction) => TransactionDTO::fromModel($transaction, $wallet->address));
    }

    public function getBlocks(Wallet $wallet): AbstractPaginator
    {
        return Block::where('proposer', $wallet->address)
            ->withScope(OrderByHeightScope::class)
            ->paginate($this->perPage(), page: $this->page())
            ->through(fn (Block $block) => BlockDTO::fromModel($block));
    }

    public function getVoters(Wallet $wallet): AbstractPaginator
    {
        return Wallet::where('attributes->vote', $wallet->address)
            ->withScope(OrderByBalanceScope::class)
            ->paginate($this->perPage(), page: $this->page())
            ->through(fn (Wallet $voter) => WalletDTO::fromModel($voter));
    }

    private function page(): int
    {
        return (int) request()->get('page', 1);
    }

    private function perPage(): int
    {
        return (int) request()->get('per-page', 25);
    }

    private function filter(string $key): bool
    {
        if (request()->has($key)) {
            return request()->get($key) === 'true';
        }

        $currentTab = request()->get('tab', 'transactions');

        return Arr::get(self::FILTERS, $currentTab.'.'.$key, false);
    }

    private function filters(): array
    {
        $currentTab = request()->get('tab', 'transactions');

        $filters = Arr::get(self::FILTERS, $currentTab, []);
        foreach ($filters as $key => $value) {
            $filters[$key] = $this->filter($key);
        }

        return $filters;
    }

    private function getTransactionsQuery(Wallet $wallet): Builder
    {
        return Transaction::query()
            ->withTypeFilter($this->filters())
            ->with('votedFor')
            ->where(function ($query) use ($wallet) {
                $query->where(fn ($query) => $query->when($this->filter('outgoing'), fn ($query) => $query->where('sender_public_key', $wallet->public_key)))
                    ->orWhere(fn ($query) => $query->when($this->filter('incoming'), fn ($query) => $query->where('to', $wallet->address)))
                    ->orWhere(function ($query) use ($wallet) {
                        $query->when($this->filter('multipayments'), function ($query) use ($wallet) {
                            $query->withScope(HasMultiPaymentRecipientScope::class, $wallet->address);
                        });
                    });
            });
    }

    private function hasAddressingFilters(): bool
    {
        if ($this->filter('incoming') === true) {
            return true;
        }

        return $this->filter('outgoing') === true;
    }

    private function hasTransactionTypeFilters(): bool
    {
        if ($this->filter('transfers') === true) {
            return true;
        }

        if ($this->filter('multipayments') === true) {
            return true;
        }

        if ($this->filter('votes') === true) {
            return true;
        }

        if ($this->filter('validator') === true) {
            return true;
        }

        if ($this->filter('username') === true) {
            return true;
        }

        if ($this->filter('contract_deployment') === true) {
            return true;
        }

        return $this->filter('others') === true;
    }

    private function getTransactionsNoResultsMessageProperty(int $count): null|string
    {
        if (! $this->hasAddressingFilters() && ! $this->hasTransactionTypeFilters()) {
            return trans('tables.transactions.no_results.no_filters');
        }

        if (! $this->hasAddressingFilters()) {
            return trans('tables.transactions.no_results.no_addressing_filters');
        }

        if ($count === 0) {
            return trans('tables.transactions.no_results.no_results');
        }

        return null;
    }

    private function getValidatedBlocksNoResultsMessageProperty(int $count): null|string
    {
        if ($count === 0) {
            return trans('tables.wallet.blocks.no_results');
        }

        return null;
    }

    private function getVotersNoResultsMessageProperty(int $count): null|string
    {
        if ($count === 0) {
            return trans('tables.wallets.no_results');
        }

        return null;
    }
}
