<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Enums\ContractMethod;
use App\Models\Transaction;

final class TransactionMethod
{
    private ?string $methodHash;

    private array $types = [
        'isTransfer'              => 'transfer',
        'isTokenTransfer'         => 'transfer',
        'isMultiPayment'          => 'multipayment',
        'isUnvote'                => 'unvote',
        'isVote'                  => 'vote',
        'isValidatorRegistration' => 'validator-registration',
        'isValidatorResignation'  => 'validator-resignation',
        'isValidatorUpdate'       => 'validator-update',
        'isUsernameRegistration'  => 'username-registration',
        'isUsernameResignation'   => 'username-resignation',
        'isContractDeployment'    => 'contract-deployment',
    ];

    public function __construct(private Transaction $transaction)
    {
        $this->methodHash = $transaction->methodHash();
    }

    public function name(): string
    {
        foreach ($this->types as $method => $name) {
            if ((bool) call_user_func_safe([$this, $method])) {
                return trans('general.transaction.types.'.$name);
            }
        }

        if (app('translator')->has('contracts.'.$this->methodHash)) {
            /** @var ?string $methodName */
            $methodName = preg_replace('/\(.+\)$/', '', trans('contracts.'.$this->methodHash));

            if ($methodName !== null) {
                return $methodName;
            }
        }

        return '0x'.$this->methodHash;
    }

    public function isTransfer(): bool
    {
        return $this->methodHash === null;
    }

    public function isTokenTransfer(): bool
    {
        return $this->methodHash === ContractMethod::transfer();
    }

    public function isMultiPayment(): bool
    {
        return $this->methodHash === ContractMethod::multiPayment();
    }

    public function isVote(): bool
    {
        return $this->methodHash === ContractMethod::vote();
    }

    public function isUnvote(): bool
    {
        return $this->methodHash === ContractMethod::unvote();
    }

    public function isValidatorRegistration(): bool
    {
        return $this->methodHash === ContractMethod::validatorRegistration();
    }

    public function isValidatorResignation(): bool
    {
        return $this->methodHash === ContractMethod::validatorResignation();
    }

    public function isValidatorUpdate(): bool
    {
        return $this->methodHash === ContractMethod::validatorUpdate();
    }

    public function isUsernameRegistration(): bool
    {
        return $this->methodHash === ContractMethod::usernameRegistration();
    }

    public function isUsernameResignation(): bool
    {
        return $this->methodHash === ContractMethod::usernameResignation();
    }

    public function isContractDeployment(): bool
    {
        return $this->transaction->to === null;
    }

    public function arguments(): array
    {
        $methodData = $this->transaction->getMethodData();
        if ($methodData === null) {
            return [];
        }

        [2 => $arguments] = $methodData;

        return $arguments;
    }
}
