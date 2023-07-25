<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Block;

use App\Services\ExchangeRate;
use App\ViewModels\TransactionViewModel;
use Illuminate\Support\Collection;

trait HasTransactions
{
    public function transactionCount(): int
    {
        return $this->block->number_of_transactions;
    }

    public function transactions(): Collection
    {
        return $this->block->transactions
            ->map(fn ($transaction) => new TransactionViewModel($transaction));
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
        return $this->block->total_fee->toFloat();
    }

    public function feeFiat(): string
    {
        return ExchangeRate::convert($this->block->total_fee->toFloat(), $this->block->timestamp);
    }

    public function totalReward(): float
    {
        return  $this->block->reward->plus($this->block->total_fee->valueOf())->toFloat();
    }

    public function totalRewardFiat(): string
    {
        return ExchangeRate::convert(
            $this->block->reward->plus($this->block->total_fee->valueOf())->toFloat(),
            $this->block->timestamp
        );
    }
}
