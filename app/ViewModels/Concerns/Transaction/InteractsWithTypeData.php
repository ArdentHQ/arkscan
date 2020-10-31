<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Services\Transactions\TransactionTypeComponent;

trait InteractsWithTypeData
{
    public function typeLabel(): string
    {
        return trans('general.transaction.'.$this->iconType());
    }

    public function headerComponent(): string
    {
        return (new TransactionTypeComponent($this->transaction))->header();
    }

    public function typeComponent(): string
    {
        return (new TransactionTypeComponent($this->transaction))->details();
    }

    public function extensionComponent(): string
    {
        return (new TransactionTypeComponent($this->transaction))->extension();
    }

    public function hasExtraData(): bool
    {
        if ($this->isMultiSignature()) {
            return true;
        }

        if ($this->isVoteCombination()) {
            return true;
        }

        if ($this->isMultiPayment()) {
            return true;
        }

        return false;
    }
}
