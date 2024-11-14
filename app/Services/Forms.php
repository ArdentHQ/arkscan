<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Collection;

final class Forms
{
    protected static array $transactionOptionsValues = [
        'all',
        'transfer',
        'validatorRegistration',
        'validatorResignation',
        'vote',
        'multiSignature',
        'multiPayment',
    ];

    /**
     * Map the transaction type options as the rich select component expects.
     *
     * return array
     */
    public static function getTransactionOptions(): array
    {
        /** @var Collection<string, string> $options */
        $options = collect(static::$transactionOptionsValues)
            ->mapWithKeys(fn ($option) => [$option =>__('forms.search.transaction_types.'.$option)]);

        return $options->toArray();
    }
}
