<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Models\Transaction;

final class TransactionTypeIcon
{
    private TransactionType $type;

    private array $types = [
        'isTransfer'                       => 'transfer',
        'isSecondSignature'                => 'second-signature',
        'isValidatorRegistration'          => 'validator-registration',
        'isVoteCombination'                => 'vote-combination',
        'isUnvote'                         => 'unvote',
        'isVote'                           => 'vote',
        'isMultiSignature'                 => 'multi-signature',
        'isIpfs'                           => 'ipfs',
        'isValidatorResignation'           => 'validator-resignation',
        'isMultiPayment'                   => 'multi-payment',
        'isUsernameRegistration'           => 'validator-registration',
        'isUsernameResignation'            => 'validator-resignation',
        'isBusinessEntityRegistration'     => 'business',
        'isBusinessEntityResignation'      => 'business',
        'isBusinessEntityUpdate'           => 'business',
        'isProductEntityRegistration'      => 'product',
        'isProductEntityResignation'       => 'product',
        'isProductEntityUpdate'            => 'product',
        'isPluginEntityRegistration'       => 'plugin',
        'isPluginEntityResignation'        => 'plugin',
        'isPluginEntityUpdate'             => 'plugin',
        'isModuleEntityRegistration'       => 'module',
        'isModuleEntityResignation'        => 'module',
        'isModuleEntityUpdate'             => 'module',
        'isValidatorEntityRegistration'    => 'validator-registration',
        'isValidatorEntityResignation'     => 'validator-registration',
        'isValidatorEntityUpdate'          => 'validator-registration',
        'isLegacyBusinessRegistration'     => 'business',
        'isLegacyBusinessResignation'      => 'business',
        'isLegacyBusinessUpdate'           => 'business',
        'isLegacyBridgechainRegistration'  => 'bridgechain',
        'isLegacyBridgechainResignation'   => 'bridgechain',
        'isLegacyBridgechainUpdate'        => 'bridgechain',
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
