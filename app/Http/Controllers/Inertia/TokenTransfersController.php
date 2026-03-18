<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\TokenAction as TokenActionDTO;
use App\Http\Controllers\Inertia\Concerns\WithPagination;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\TokenAction;
use ARKEcosystem\Foundation\UserInterface\UI;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

final class TokenTransfersController
{
    use WithPagination;

    public function __invoke(): Response
    {
        return Inertia::renderWithMeta('Tokens/Transfers', 'token-transfers', [
            'transfers' => Inertia::lazy(function () {
                $paginator = $this->getTransactions();

                return [
                    ...$paginator->toArray(),

                    'meta'             => UI::getPaginationData($paginator),
                    'noResultsMessage' => $this->getNoResultsMessageProperty($paginator->count()),
                ];
            }),
        ]);
    }

    public function getNoResultsMessageProperty(int $count): null|string
    {
        return $count === 0
            ? (string) trans('tables.tokens.transfers.no_results')
            : null;
    }

    public function getTransactions(): LengthAwarePaginator
    {
        return TokenAction::select('token_actions.*')
            ->with(['token', 'transaction.sender', 'transaction.senderWallet', 'transaction.recipientWallet'])
            ->join('transactions', 'transactions.hash', '=', 'token_actions.transaction_hash')
            ->withScope(OrderByTimestampScope::class)
            ->paginate($this->perPage())
            ->through(fn (TokenAction $transaction) => TokenActionDTO::fromModel($transaction));
    }
}
