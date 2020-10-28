<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Block;

use App\Facades\Network;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;

trait HasTransactions
{
    public function transactionCount(): int
    {
        return $this->block->number_of_transactions;
    }

    public function amount(): string
    {
        return NumberFormatter::currency($this->block->total_amount->toFloat(), Network::currency());
    }

    public function amountFiat(): string
    {
        return ExchangeRate::convert($this->block->total_amount->toFloat(), $this->block->timestamp);
    }

    public function fee(): string
    {
        return NumberFormatter::currency($this->block->total_fee->toFloat(), Network::currency());
    }

    public function feeFiat(): string
    {
        return ExchangeRate::convert($this->block->total_fee->toFloat(), $this->block->timestamp);
    }
}
