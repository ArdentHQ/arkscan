<?php

declare(strict_types=1);

namespace App\DTO\Inertia;

use App\DTO\Inertia\Wallet as WalletDTO;
use App\Facades\Wallets;
use App\Models\Transaction as Model;
use App\ViewModels\TransactionViewModel;

class Transaction
{
    private TransactionViewModel $viewModel;

    public function __construct(public Model $transaction, public ?string $address = null)
    {
        $this->viewModel = new TransactionViewModel($transaction);
    }

    public function toArray(): array
    {
        $votedFor = null;
        if ($this->viewModel->isVote()) {
            $votedFor = $this->viewModel->voted();
            if ($votedFor !== null) {
                $votedFor = $votedFor->address();
            }
        }

        $sender = null;
        $senderAddress = $this->viewModel->sender()?->address();
        if ($senderAddress !== null) {
            $senderWallet = Wallets::findByAddress($senderAddress);

            $sender = (new WalletDTO($senderWallet))->toArray();
        }

        $recipient = null;
        $recipientAddress = $this->viewModel->recipient()?->address();
        if ($recipientAddress !== null) {
            $recipientWallet = Wallets::findByAddress($recipientAddress);

            $recipient = (new WalletDTO($recipientWallet))->toArray();
        }

        $validatorRegistration = null;
        $validatorRegistrationTransaction = $this->viewModel->validatorRegistration();
        if ($validatorRegistrationTransaction !== null) {
            $validatorRegistration = (new self($validatorRegistrationTransaction->model()))->toArray();
        }

        $address = $this->address ?? $this->transaction->from;

        return [
            'hash'                      => $this->transaction->hash,
            'block_hash'                => $this->transaction->block_hash,
            'block_number'              => $this->transaction->block_number,
            'transaction_index'         => $this->transaction->transaction_index,
            'timestamp'                 => $this->transaction->timestamp,
            'nonce'                     => $this->transaction->nonce,
            'sender_public_key'         => $this->transaction->sender_public_key,
            'from'                      => $this->transaction->from,
            'to'                        => $this->transaction->to,
            'value'                     => (string) $this->transaction->value,
            'gas_price'                 => (string) $this->transaction->gas_price,
            'gas'                       => (string) $this->transaction->gas,
            'status'                    => $this->transaction->status,
            'gas_used'                  => (string) $this->transaction->gas_used,
            'gas_refunded'              => (string) $this->transaction->gas_refunded,
            'deployed_contract_address' => $this->transaction->deployed_contract_address,
            'logs'                      => $this->transaction->logs,
            'decoded_error'             => $this->transaction->decoded_error,
            'multi_payment_recipients'  => $this->transaction->multi_payment_recipients,

            // ViewModel Data
            'amount'                    => $this->viewModel->amount(),
            'amountForItself'           => $this->viewModel->amountForItself(),
            'amountExcludingItself'     => $this->viewModel->amountExcludingItself(),
            'amountWithFee'             => $this->viewModel->amountWithFee(),
            'amountReceived'            => $this->viewModel->amountReceived(),
            'amountFiat'                => $this->viewModel->amountFiat(true),
            'amountReceivedFiat'        => $this->viewModel->amountReceivedFiat($address),

            'fee'                       => $this->viewModel->fee(),
            'feeFiat'                   => $this->viewModel->feeFiat(true),
            'type'                      => $this->viewModel->typeName(),
            'isTransfer'                => $this->viewModel->isTransfer(),
            'isTokenTransfer'           => $this->viewModel->isTokenTransfer(),
            'isVote'                    => $this->viewModel->isVote(),
            'isUnvote'                  => $this->viewModel->isUnvote(),
            'isValidatorRegistration'   => $this->viewModel->isValidatorRegistration(),
            'isValidatorResignation'    => $this->viewModel->isValidatorResignation(),
            'isValidatorUpdate'         => $this->viewModel->isValidatorUpdate(),
            'isUsernameRegistration'    => $this->viewModel->isUsernameRegistration(),
            'isUsernameResignation'     => $this->viewModel->isUsernameResignation(),
            'isContractDeployment'      => $this->viewModel->isContractDeployment(),
            'isMultiPayment'            => $this->viewModel->isMultiPayment(),
            'isSelfReceiving'           => $this->viewModel->isSelfReceiving(),
            'isSent'                    => $this->viewModel->isSent($address),
            'isSentToSelf'              => $this->viewModel->isSentToSelf($address),
            'isReceived'                => $this->viewModel->isReceived($address),
            'hasFailedStatus'           => $this->viewModel->hasFailedStatus(),
            'validatorRegistration'     => $validatorRegistration,

            'votedFor'                  => $votedFor,
            'sender'                    => $sender,
            'recipient'                 => $recipient,
        ];
    }
}
