<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Models\Transaction;

final class TransactionTypeIcon
{
    private TransactionType $type;

    private array $types = [
        'isTransfer'              => 'transfer',
        'isValidatorRegistration' => 'validator-registration',
        'isVoteCombination'       => 'vote-combination',
        'isUnvote'                => 'unvote',
        'isVote'                  => 'vote',
        'isMultiSignature'        => 'multi-signature',
        'isValidatorResignation'  => 'validator-resignation',
        'isMultiPayment'          => 'multi-payment',
        'isUsernameRegistration'  => 'validator-registration',
        'isUsernameResignation'   => 'validator-resignation',
    ];

    public function __construct(Transaction $transaction)
    {
        $this->type = new TransactionType($transaction);
    }

    public function name(): string
    {
        foreach ($this->types as $method => $icon) {
            if ((bool) call_user_func_safe([$this->type, $method])) {
                return $icon;
            }
        }

        return 'unknown';
    }
}
