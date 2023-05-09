<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Models\Transaction;

final class TransactionTypeIcon
{
    private TransactionType $type;

    private array $types = [
        'isTransfer'                      => 'transfer',
        'isSecondSignature'               => 'second-signature',
        'isDelegateRegistration'          => 'delegate-registration',
        'isVoteCombination'               => 'vote-combination',
        'isUnvote'                        => 'unvote',
        'isVote'                          => 'vote',
        'isMultiSignature'                => 'multi-signature',
        'isIpfs'                          => 'ipfs',
        'isDelegateResignation'           => 'delegate-resignation',
        'isMultiPayment'                  => 'multi-payment',
        'isTimelock'                      => 'timelock',
        'isTimelockClaim'                 => 'timelock',
        'isTimelockRefund'                => 'timelock',
        'isBusinessEntityRegistration'    => 'business',
        'isBusinessEntityResignation'     => 'business',
        'isBusinessEntityUpdate'          => 'business',
        'isProductEntityRegistration'     => 'product',
        'isProductEntityResignation'      => 'product',
        'isProductEntityUpdate'           => 'product',
        'isPluginEntityRegistration'      => 'plugin',
        'isPluginEntityResignation'       => 'plugin',
        'isPluginEntityUpdate'            => 'plugin',
        'isModuleEntityRegistration'      => 'module',
        'isModuleEntityResignation'       => 'module',
        'isModuleEntityUpdate'            => 'module',
        'isDelegateEntityRegistration'    => 'delegate-registration',
        'isDelegateEntityResignation'     => 'delegate-registration',
        'isDelegateEntityUpdate'          => 'delegate-registration',
        'isLegacyBusinessRegistration'    => 'business',
        'isLegacyBusinessResignation'     => 'business',
        'isLegacyBusinessUpdate'          => 'business',
        'isLegacyBridgechainRegistration' => 'bridgechain',
        'isLegacyBridgechainResignation'  => 'bridgechain',
        'isLegacyBridgechainUpdate'       => 'bridgechain',
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
