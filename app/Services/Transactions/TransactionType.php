<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Enums\TransactionTypeEnum;
use App\Models\Transaction;
use App\ViewModels\Concerns\Transaction\InteractsWithVendorField;
use Illuminate\Support\Arr;

final class TransactionType
{
    use InteractsWithVendorField;

    private array $types = [
        'isTransfer'              => 'transfer',
        'isValidatorRegistration' => 'validator-registration',
        'isUsernameRegistration'  => 'username-registration',
        'isUsernameResignation'   => 'username-resignation',
        'isVoteCombination'       => 'vote-combination',
        'isUnvote'                => 'unvote',
        'isVote'                  => 'vote',
        'isMultiSignature'        => 'multi-signature',
        'isValidatorResignation'  => 'validator-resignation',
        'isMultiPayment'          => 'multi-payment',
    ];

    public function __construct(private Transaction $transaction)
    {
    }

    public function name(): string
    {
        foreach ($this->types as $method => $name) {
            if ((bool) call_user_func_safe([$this, $method])) {
                return $name;
            }
        }

        return 'unknown';
    }

    public function isTransfer(): bool
    {
        return $this->transaction->type === TransactionTypeEnum::TRANSFER;
    }

    public function isValidatorRegistration(): bool
    {
        return $this->transaction->type === TransactionTypeEnum::VALIDATOR_REGISTRATION;
    }

    public function isVote(): bool
    {
        return $this->determineVoteTypes()[0] === true;
    }

    public function isUnvote(): bool
    {
        return $this->determineVoteTypes()[1] === true;
    }

    public function isVoteCombination(): bool
    {
        [$containsVote, $containsUnvote] = $this->determineVoteTypes();

        return $containsVote && $containsUnvote;
    }

    public function isMultiSignature(): bool
    {
        return $this->transaction->type === TransactionTypeEnum::MULTI_SIGNATURE;
    }

    public function isValidatorResignation(): bool
    {
        return $this->transaction->type === TransactionTypeEnum::VALIDATOR_RESIGNATION;
    }

    public function isMultiPayment(): bool
    {
        return $this->transaction->type === TransactionTypeEnum::MULTI_PAYMENT;
    }

    public function isUsernameRegistration(): bool
    {
        return $this->transaction->type === TransactionTypeEnum::USERNAME_REGISTRATION;
    }

    public function isUsernameResignation(): bool
    {
        return $this->transaction->type === TransactionTypeEnum::USERNAME_RESIGNATION;
    }

    public function isUnknown(): bool
    {
        if ($this->isTransfer()) {
            return false;
        }

        if ($this->isValidatorRegistration()) {
            return false;
        }

        if ($this->isVoteCombination()) {
            return false;
        }

        if ($this->isUnvote()) {
            return false;
        }

        if ($this->isVote()) {
            return false;
        }

        if ($this->isMultiSignature()) {
            return false;
        }

        if ($this->isValidatorResignation()) {
            return false;
        }

        if ($this->isMultiPayment()) {
            return false;
        }

        if ($this->isUsernameRegistration()) {
            return false;
        }

        if ($this->isUsernameResignation()) {
            return false;
        }

        return true;
    }

    private function determineVoteTypes(): array
    {
        $containsVote   = false;
        $containsUnvote = false;

        if ($this->transaction->type !== TransactionTypeEnum::VOTE) {
            return [$containsVote, $containsUnvote];
        }

        if (! is_array($this->transaction->asset)) {
            return [$containsVote, $containsUnvote];
        }

        $containsVote   = count(Arr::get($this->transaction->asset, 'votes', [])) !== 0;
        $containsUnvote = count(Arr::get($this->transaction->asset, 'unvotes', [])) !== 0;

        return [$containsVote, $containsUnvote];
    }
}
