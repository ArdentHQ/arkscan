<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Block;

use App\Services\ExchangeRate;
use App\ViewModels\TransactionViewModel;

trait HasTransactions
{
    public function transactionCount(): int
    {
        return $this->block->transactions_count;
    }

    public function amount(): float
    {
        $amount = 0;
        foreach ($this->block->transactions as $transaction) {
            $amount += (new TransactionViewModel($transaction))->amount();
        }

        return $amount;
    }

    public function amountFiat(): string
    {
        return ExchangeRate::convert($this->amount(), $this->block->timestamp);
    }

    public function fee(): float
    {
        return $this->block->fee->toFloat();
    }

    public function feeFiat(): string
    {
        return ExchangeRate::convert($this->block->fee->toFloat(), $this->block->timestamp);
    }

    public function totalReward(): float
    {
        return  $this->block->reward->plus($this->block->fee->valueOf())->toFloat();
    }

    public function totalRewardFiat(): string
    {
        return ExchangeRate::convert(
            $this->block->reward->plus($this->block->fee->valueOf())->toFloat(),
            $this->block->timestamp
        );
    }
}
