<?php

declare(strict_types=1);

namespace App\DTO;

use App\Facades\Wallets;
use App\Services\ExchangeRate;
use Illuminate\Support\Arr;

final class Payment
{
    private float $amount;

    private string $address;

    private ?string $username = null;

    public function __construct(private int $timestamp, array $payment)
    {
        $this->amount   = $payment['amount'] / config('currencies.notation.crypto', 1e18);
        $this->address  = $payment['recipientId'];
        $this->username = Arr::get(Wallets::findByAddress($payment['recipientId']), 'attributes.username');
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function username(): ?string
    {
        return $this->username;
    }

    public function amountFiat(): string
    {
        return ExchangeRate::convert($this->amount, $this->timestamp);
    }

    public function address(): string
    {
        return $this->address;
    }

    public function recipient(): self
    {
        return $this;
    }
}
