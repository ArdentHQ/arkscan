<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Block as BlockDTO;
use App\DTO\Inertia\TokenAction as TokenActionDTO;
use App\DTO\Inertia\TokenHolder as TokenHolderDTO;
use App\DTO\Inertia\Transaction as TransactionDTO;
use App\DTO\Inertia\Wallet as WalletDTO;
use App\Enums\TokenActionType;
use App\Http\Controllers\Inertia\Concerns\WithFilters;
use App\Http\Controllers\Inertia\Concerns\WithPagination;
use App\Http\Controllers\Inertia\Concerns\WithWalletRelations;
use App\Models\Block;
use App\Models\Scopes\HasMultiPaymentRecipientScope;
use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Scopes\OrderByHeightScope;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\Scopes\OrderByTransactionIndexScope;
use App\Models\Scopes\OrderByWhitelistedTokensFirstScope;
use App\Models\TokenAction;
use App\Models\TokenHolder;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Services\ExchangeRate;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

final class WalletController
{
    use WithFilters;
    use WithPagination;
    use WithWalletRelations;

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

    private string $view = 'transactions';

    public function __invoke(Wallet $wallet, string $view = 'transactions'): Response
    {
        $this->view = $view;

        return Inertia::renderWithMeta('Wallet/Wallet', 'wallet', [
            'wallet'             => fn () => WalletDTO::fromModel($wallet),
            'filters'            => self::FILTERS,
            'tokenHoldingsCount' => fn () => $this->getTokenHoldingsCount($wallet),
            'baseUrl'            => route('wallet', $wallet->address, false),

            'transactions' => Inertia::optional(function () use ($wallet) {
                $paginator = $this->getTransactions($wallet);

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getTransactionsNoResultsMessageProperty($paginator->count()),
                ];
            }),

            'tokenTransfers' => Inertia::optional(function () use ($wallet) {
                $paginator = $this->getTokenTransfers($wallet);

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getTokenTransfersNoResultsMessageProperty($paginator->count()),
                ];
            }),

            'tokens' => Inertia::optional(function () use ($wallet) {
                $paginator = $this->getTokens($wallet);

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getTokensNoResultsMessageProperty($paginator->count()),
                ];
            }),

            'blocks' => Inertia::optional(function () use ($wallet) {
                $paginator = $this->getBlocks($wallet);

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getValidatedBlocksNoResultsMessageProperty($paginator->count()),
                ];
            }),

            'voters' => Inertia::optional(function () use ($wallet) {
                $paginator = $this->getVoters($wallet);

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getVotersNoResultsMessageProperty($paginator->count()),
                ];
            }),

            'rates' => fn () => ExchangeRate::rates()->toArray(),
        ], [
            'address' => $wallet->address,
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

        $paginator = $this->getTransactionsQuery($wallet)
            ->withScope(OrderByTimestampScope::class)
            ->withScope(OrderByTransactionIndexScope::class)
            ->paginate($this->perPage(), page: $this->page());

        $this->loadWalletRelations($paginator);

        return $paginator->through(fn (Transaction $transaction) => TransactionDTO::fromModel($transaction, $wallet->address));
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

    public function getTokenTransfers(Wallet $wallet): LengthAwarePaginator
    {
        return TokenAction::select('token_actions.*')
            ->with(['token', 'transaction.sender', 'transaction.senderWallet', 'transaction.recipientWallet'])
            ->join('transactions', 'transactions.hash', '=', 'token_actions.transaction_hash')
            ->where(function (Builder $query) use ($wallet) {
                $query->where('token_actions.to', $wallet->address)
                    ->orWhere('token_actions.from', $wallet->address);
            })
            ->where('token_actions.action', TokenActionType::Transfer)
            ->withScope(OrderByTimestampScope::class)
            ->paginate($this->perPage())
            ->through(fn (TokenAction $transaction) => TokenActionDTO::fromModel($transaction));
    }

    public function getTokens(Wallet $wallet): LengthAwarePaginator
    {
        return TokenHolder::with(['token'])
            ->where('address', $wallet->address)
            ->withScope(OrderByWhitelistedTokensFirstScope::class)
            ->orderBy('balance', 'desc')
            ->paginate($this->perPage())
            ->through(fn (TokenHolder $tokenHolder) => TokenHolderDTO::fromModel($tokenHolder));
    }

    public function getTokenTransfersNoResultsMessageProperty(int $count): null|string
    {
        return $count === 0
            ? (string) trans('tables.tokens.transfers.no_results')
            : null;
    }

    public function getTokensNoResultsMessageProperty(int $count): null|string
    {
        return $count === 0
            ? (string) trans('tables.tokens.no_results')
            : null;
    }

    public function getTokenHoldingsCount(Wallet $wallet): int
    {
        return (int) Cache::remember('token_holdings_count_'.$wallet->address, now()->addMinutes(10), function () use ($wallet) {
            return TokenHolder::where('address', $wallet->address)->count();
        });
    }

    /**
     * @return Builder<Transaction>
     */
    private function getTransactionsQuery(Wallet $wallet): Builder
    {
        $filters = $this->filters($this->view);

        // Each address condition as a separate SELECT hash query so PostgreSQL
        // can use individual indexes instead of a slow OR-based plan.
        $hashQueries = [];

        if ($filters['outgoing'] && $wallet->public_key !== null) {
            $hashQueries[] = Transaction::query()
                ->withTypeFilter($filters)
                ->where('sender_public_key', $wallet->public_key)
                ->select('hash');
        }

        if ($filters['incoming']) {
            $hashQueries[] = Transaction::query()
                ->withTypeFilter($filters)
                ->where('to', $wallet->address)
                ->select('hash');

            if ($filters['multipayments']) {
                $hashQueries[] = Transaction::query()
                    ->withTypeFilter($filters)
                    ->withScope(HasMultiPaymentRecipientScope::class, $wallet->address)
                    ->select('hash');
            }

            if ($filters['transfers']) {
                $hashQueries[] = TokenAction::query()
                    ->where('to', $wallet->address)
                    ->select('transaction_hash as hash');
            }
        }

        if ($hashQueries === []) {
            return Transaction::query()->whereRaw('1 = 0');
        }

        $union = array_shift($hashQueries);
        foreach ($hashQueries as $query) {
            $union = $union->unionAll($query);
        }

        return Transaction::query()
            ->withTypeFilter($filters)
            ->with(['multiPaymentRecipients'])
            ->whereIn('hash', $union);
    }

    private function hasAddressingFilters(): bool
    {
        $filters = $this->filters('transactions');
        if ($filters['incoming'] === true) {
            return true;
        }

        return $filters['outgoing'] === true;
    }

    private function hasTransactionTypeFilters(): bool
    {
        $filters = $this->filters('transactions');
        if ($filters['transfers'] === true) {
            return true;
        }

        if ($filters['multipayments'] === true) {
            return true;
        }

        if ($filters['votes'] === true) {
            return true;
        }

        if ($filters['validator'] === true) {
            return true;
        }

        if ($filters['username'] === true) {
            return true;
        }

        if ($filters['contract_deployment'] === true) {
            return true;
        }

        return $filters['others'] === true;
    }

    private function getTransactionsNoResultsMessageProperty(int $count): null|string
    {
        $hasAddressingFilters = $this->hasAddressingFilters();
        if (! $hasAddressingFilters && ! $this->hasTransactionTypeFilters()) {
            return trans('tables.transactions.no_results.no_filters');
        }

        if (! $hasAddressingFilters) {
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
