<?php

declare(strict_types=1);

namespace App\Http\Controllers\Inertia;

use App\DTO\Inertia\Transaction as TransactionDTO;
use App\Enums\TokenTransferArgument;
use App\Models\MultiPayment;
use App\Models\Transaction;
use App\Services\ExchangeRate;
use App\ViewModels\TransactionViewModel;
use ArkEcosystem\Crypto\Utils\Abi\ArgumentDecoder;
use Inertia\Inertia;
use Inertia\Response;

final class ShowTransactionController
{
    public function __invoke(Transaction $transaction): Response
    {
        $transaction->loadMissing('votedFor', 'multiPaymentRecipients');

        $viewModel = new TransactionViewModel($transaction);

        return Inertia::render('Transaction/Show', [
            'transaction' => TransactionDTO::fromModel($transaction),
            'details'     => [
                'confirmations'         => $viewModel->confirmations(),
                'transactionError'      => $viewModel->transactionError(),
                'recipientIsContract'   => $viewModel->recipient()?->isContract() ?? false,
                'validatorPublicKey'    => $viewModel->validatorPublicKey(),
                'username'              => $viewModel->username(),
                'tokenTransfer'         => $this->tokenTransferDetails($viewModel),
                'payload'               => $this->payloadDetails($viewModel),
                'multiPaymentRecipients' => $this->multiPaymentRecipients($viewModel),
                'totalFiat'             => $viewModel->totalFiat(true),
                'totalFiatValue'        => ExchangeRate::convertNumerical($viewModel->amountWithFee(), $transaction->timestamp),
            ],
        ])->withMeta('transaction', [
            'txid' => $transaction->hash,
        ]);
    }

    /**
     * @return array{recipient: string, amount: string|null}|null
     */
    private function tokenTransferDetails(TransactionViewModel $transaction): ?array
    {
        if (! $transaction->isTokenTransfer()) {
            return null;
        }

        $arguments = $transaction->methodArguments();
        if (count($arguments) === 0 || ! array_key_exists(TokenTransferArgument::RECIPIENT, $arguments)) {
            return null;
        }

        $recipient = (new ArgumentDecoder($arguments[TokenTransferArgument::RECIPIENT]))->decodeAddress();

        $amount = null;
        if (array_key_exists(TokenTransferArgument::AMOUNT, $arguments)) {
            $amount = (new ArgumentDecoder($arguments[TokenTransferArgument::AMOUNT]))->decodeUnsignedInt();
        }

        return [
            'recipient' => $recipient,
            'amount'    => $amount,
        ];
    }

    /**
     * @return array{formatted: ?string, utf8: ?string, raw: ?string}|null
     */
    private function payloadDetails(TransactionViewModel $transaction): ?array
    {
        if (! $transaction->hasPayload()) {
            return null;
        }

        return [
            'formatted' => $transaction->formattedPayload(),
            'utf8'      => $transaction->utf8Payload(),
            'raw'       => $transaction->rawPayload(),
        ];
    }

    /**
     * @return array<int, array{address: string, amount: string}>
     */
    private function multiPaymentRecipients(TransactionViewModel $transaction): array
    {
        return $transaction->multiPaymentRecipients()
            ->map(fn (MultiPayment $recipient) => [
                'address' => $recipient->to,
                'amount'  => (string) $recipient->amount,
            ])
            ->values()
            ->toArray();
    }
}
