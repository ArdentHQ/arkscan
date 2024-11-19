<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Enums\PayloadSignature;
use App\Models\Transaction;
use App\ViewModels\Concerns\Transaction\HasPayload;

final class TransactionMethod
{
    use HasPayload;

    private ?string $methodHash;

    private array $types = [
        'isTransfer'              => 'transfer',
        'isValidatorRegistration' => 'validator-registration',
        'isUnvote'                => 'unvote',
        'isVote'                  => 'vote',
        'isValidatorResignation'  => 'validator-resignation',
    ];

    public function __construct(private Transaction $transaction)
    {
        $this->methodHash = $this->methodHash();
    }

    public function name(): string
    {
        foreach ($this->types as $method => $name) {
            if ((bool) call_user_func_safe([$this, $method])) {
                return trans('general.transaction.types.'.$name);
            }
        }

        if (app('translator')->has('contracts.'.$this->methodHash)) {
            return preg_replace('/\(.+\)$/', '', trans('contracts.'.$this->methodHash));
        }

        return '0x'.$this->methodHash;
    }

    public function isTransfer(): bool
    {
        if ($this->methodHash === null) {
            return true;
        }

        return $this->methodHash === PayloadSignature::TRANSFER->value;
    }

    public function isValidatorRegistration(): bool
    {
        return $this->methodHash === PayloadSignature::VALIDATOR_REGISTRATION->value;
    }

    public function isVote(): bool
    {
        return $this->methodHash === PayloadSignature::VOTE->value;
    }

    public function isUnvote(): bool
    {
        return $this->methodHash === PayloadSignature::UNVOTE->value;
    }

    public function isValidatorResignation(): bool
    {
        return $this->methodHash === PayloadSignature::VALIDATOR_RESIGNATION->value;
    }
}
