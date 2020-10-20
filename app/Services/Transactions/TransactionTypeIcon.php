<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Models\Transaction;
use Illuminate\Support\Str;

final class TransactionTypeIcon
{
    private TransactionType $type;

    private array $types = [
        'isTransfer',
        'isSecondSignature',
        'isDelegateRegistration',
        'isVote',
        'isMultiSignature',
        'isIpfs',
        'isDelegateResignation',
        'isMultiPayment',
        'isTimelock',
        'isTimelockClaim',
        'isTimelockRefund',
        'isBusinessEntityRegistration',
        'isBusinessEntityResignation',
        'isBusinessEntityUpdate',
        'isProductEntityRegistration',
        'isProductEntityResignation',
        'isProductEntityUpdate',
        'isPluginEntityRegistration',
        'isPluginEntityResignation',
        'isPluginEntityUpdate',
        'isModuleEntityRegistration',
        'isModuleEntityResignation',
        'isModuleEntityUpdate',
        'isDelegateEntityRegistration',
        'isDelegateEntityResignation',
        'isDelegateEntityUpdate',
        'isLegacyBusinessRegistration',
        'isLegacyBusinessResignation',
        'isLegacyBusinessUpdate',
        'isLegacyBridgechainRegistration',
        'isLegacyBridgechainResignation',
        'isLegacyBridgechainUpdate',
        // Run these last because they are generic.
        'isEntityRegistration',
        'isEntityResignation',
        'isEntityUpdate',
    ];

    public function __construct(Transaction $transaction)
    {
        $this->type = new TransactionType($transaction);
    }

    public function name(): string
    {
        foreach ($this->types as $type) {
            if ($this->type->$type()) {
                return str_replace('_', '-', Str::snake(substr($type, 2)));
            }
        }

        return 'unknown';
    }
}
