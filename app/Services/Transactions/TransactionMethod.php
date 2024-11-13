<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Enums\PayloadSignature;
use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use App\ViewModels\Concerns\Transaction\HasPayload;
use Illuminate\Support\Arr;

final class TransactionMethod
{
    use HasPayload;

    private ?string $methodHash;

    private array $types = [
        'isTransfer'              => 'transfer',
        'isValidatorRegistration' => 'validator-registration',
        // 'isUsernameRegistration'  => 'username-registration',
        // 'isUsernameResignation'   => 'username-resignation',
        'isUnvote'                => 'unvote',
        'isVote'                  => 'vote',
        // 'isMultiSignature'        => 'multi-signature',
        'isValidatorResignation'  => 'validator-resignation',
        // 'isMultiPayment'          => 'multi-payment',
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

    // public function isMultiSignature(): bool
    // {
    //     return $this->transaction->type === TransactionTypeEnum::MULTI_SIGNATURE;
    // }

    public function isValidatorResignation(): bool
    {
        return $this->methodHash === PayloadSignature::VALIDATOR_RESIGNATION->value;
    }

    // public function isMultiPayment(): bool
    // {
    //     return $this->transaction->type === TransactionTypeEnum::MULTI_PAYMENT;
    // }

    // public function isUsernameRegistration(): bool
    // {
    //     return $this->transaction->type === TransactionTypeEnum::USERNAME_REGISTRATION;
    // }

    // public function isUsernameResignation(): bool
    // {
    //     return $this->transaction->type === TransactionTypeEnum::USERNAME_RESIGNATION;
    // }

    public function isUnknown(): bool
    {
        if ($this->isTransfer()) {
            return false;
        }

        if ($this->isValidatorRegistration()) {
            return false;
        }

        if ($this->isUnvote()) {
            return false;
        }

        if ($this->isVote()) {
            return false;
        }

        // if ($this->isMultiSignature()) {
        //     return false;
        // }

        if ($this->isValidatorResignation()) {
            return false;
        }

        // if ($this->isMultiPayment()) {
        //     return false;
        // }

        // if ($this->isUsernameRegistration()) {
        //     return false;
        // }

        // if ($this->isUsernameResignation()) {
        //     return false;
        // }

        return true;
    }

    // private function determineVoteTypes(): array
    // {
    //     $containsVote   = false;
    //     $containsUnvote = false;

    //     if ($this->transaction->type !== TransactionTypeEnum::VOTE) {
    //         return [$containsVote, $containsUnvote];
    //     }

    //     if (! is_array($this->transaction->asset)) {
    //         return [$containsVote, $containsUnvote];
    //     }

    //     $containsVote   = count(Arr::get($this->transaction->asset, 'votes', [])) !== 0;
    //     $containsUnvote = count(Arr::get($this->transaction->asset, 'unvotes', [])) !== 0;

    //     return [$containsVote, $containsUnvote];
    // }
}
