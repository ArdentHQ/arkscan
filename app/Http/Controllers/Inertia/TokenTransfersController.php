<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\TokenTransfer as TokenTransferDTO;
use App\Http\Controllers\Inertia\Concerns\WithPagination;
use App\Models\Scopes\OrderByTimestampScope;
use App\Models\TokenTransfer;
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
        return TokenTransfer::select('token_transfers.*')
            ->with(['transaction'])
            ->join('transactions', 'transactions.hash', '=', 'token_transfers.transaction_hash')
            ->withScope(OrderByTimestampScope::class)
            ->paginate($this->perPage())
            ->through(fn (TokenTransfer $transaction) => TokenTransferDTO::fromModel($transaction));
    }
}
