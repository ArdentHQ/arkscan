<?php

declare(strict_types=1);

namespace App\Services\Transactions\Concerns;

trait ManagesTransactionTypes
{
    private array $typesGeneric = [
        'isTransfer'              => 'transfer',
        'isValidatorRegistration' => 'validator-registration',
        'isUnvote'                => 'unvote',
        'isVote'                  => 'vote',
        'isMultiSignature'        => 'multi-signature',
        'isValidatorResignation'  => 'validator-resignation',
        'isMultiPayment'          => 'multi-payment',
    ];

    private array $typesExact = [
        'isTransfer'              => 'transfer',
        'isValidatorRegistration' => 'validator-registration',
        'isUnvote'                => 'unvote',
        'isVote'                  => 'vote',
        'isMultiSignature'        => 'multi-signature',
        'isValidatorResignation'  => 'validator-resignation',
        'isMultiPayment'          => 'multi-payment',
    ];
}
