<?php

declare(strict_types=1);

namespace App\DTO;

use App\Models\Wallet;
use App\Services\ExchangeRate;
use Illuminate\Support\Arr;

final class Payment
{
    private int $timestamp;

    private float $amount;

    private string $address;

    private ?string $username = null;

    public function __construct(int $timestamp, array $payment)
    {
        $this->timestamp   = $timestamp;
        $this->amount      = $payment['amount'] / 1e8;
        $this->address     = $payment['recipientId'];
        $this->username    = Arr::get(Wallet::where('address', $payment['recipientId'])->firstOrFail(), 'attributes.delegate.username');
    }

    public function amount(): float
    {
        return $this->amount;
    }

    public function amountFiat(): string
    {
        return ExchangeRate::convert($this->amount, $this->timestamp);
    }

    public function address(): string
    {
        return $this->address;
    }

    public function username(): ?string
    {
        return $this->username;
    }

    public function recipient(): self
    {
        return $this;
    }
}
