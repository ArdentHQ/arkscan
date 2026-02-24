<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Wallet as WalletDTO;
use App\Http\Controllers\Inertia\Concerns\WithPagination;
use App\Models\Scopes\OrderByBalanceScope;
use App\Models\Wallet;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

final class TopAccountsController
{
    use WithPagination;

    public function __invoke(): Response
    {
        return Inertia::renderWithMeta('TopAccounts/List', 'top-accounts', [
            'wallets' => Inertia::optional(function () {
                $paginator = $this->getWallets();

                return [
                    ...$paginator->toArray(),
                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->noResultsMessage($paginator->count()),
                ];
            }),
        ]);
    }

    private function getWallets(): LengthAwarePaginator
    {
        return Wallet::withScope(OrderByBalanceScope::class)
            ->paginate($this->perPage(), page: $this->page())
            ->through(fn (Wallet $wallet) => WalletDTO::fromModel($wallet));
    }

    private function noResultsMessage(int $count): ?string
    {
        if ($count === 0) {
            return trans('tables.wallets.top_accounts_no_results');
        }

        return null;
    }
}
