<?php

declare(strict_types=1);

namespace App\Services\Transactions\Concerns;

trait ManagesTransactionTypes
{
    private array $typesGeneric = [
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
        'isUsernameRegistration'           => 'username-registration',
        'isUsernameResignation'            => 'username-resignation',
        'isTimelock'                       => 'timelock',
        'isTimelockClaim'                  => 'timelock-claim',
        'isTimelockRefund'                 => 'timelock-refund',
        'isEntityRegistration'             => 'entity-registration',
        'isEntityResignation'              => 'entity-resignation',
        'isEntityUpdate'                   => 'entity-update',
    ];

    private array $typesExact = [
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
        'isUsernameRegistration'           => 'username-registration',
        'isUsernameResignation'            => 'username-resignation',
        'isTimelock'                       => 'timelock',
        'isTimelockClaim'                  => 'timelock-claim',
        'isTimelockRefund'                 => 'timelock-refund',
        'isBusinessEntityRegistration'     => 'business-entity-registration',
        'isBusinessEntityResignation'      => 'business-entity-resignation',
        'isBusinessEntityUpdate'           => 'business-entity-update',
        'isProductEntityRegistration'      => 'product-entity-registration',
        'isProductEntityResignation'       => 'product-entity-resignation',
        'isProductEntityUpdate'            => 'product-entity-update',
        'isPluginEntityRegistration'       => 'plugin-entity-registration',
        'isPluginEntityResignation'        => 'plugin-entity-resignation',
        'isPluginEntityUpdate'             => 'plugin-entity-update',
        'isModuleEntityRegistration'       => 'module-entity-registration',
        'isModuleEntityResignation'        => 'module-entity-resignation',
        'isModuleEntityUpdate'             => 'module-entity-update',
        'isValidatorEntityRegistration'    => 'validator-entity-registration',
        'isValidatorEntityResignation'     => 'validator-entity-resignation',
        'isValidatorEntityUpdate'          => 'validator-entity-update',
        'isLegacyBusinessRegistration'     => 'legacy-business-registration',
        'isLegacyBusinessResignation'      => 'legacy-business-resignation',
        'isLegacyBusinessUpdate'           => 'legacy-business-update',
        'isLegacyBridgechainRegistration'  => 'legacy-bridgechain-registration',
        'isLegacyBridgechainResignation'   => 'legacy-bridgechain-resignation',
        'isLegacyBridgechainUpdate'        => 'legacy-bridgechain-update',
    ];
}
