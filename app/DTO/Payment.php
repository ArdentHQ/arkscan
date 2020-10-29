<?php

declare(strict_types=1);

namespace App\DTO;

use App\Services\ExchangeRate;

final class Payment
{
    private int $timestamp;

    private int $amount;

    private string $address;

    private ?string $username = null;

    /* @phpstan-ignore-next-line */
    public function __construct(int $timestamp, string $amount, string $address, ?string $username = null)
    {
        $this->timestamp   = $timestamp;
        $this->amount      = (int) $amount;
        $this->address     = $address;
        $this->username    = $username;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function amountFiat(): string
    {
        return ExchangeRate::convert((float) $this->amount, $this->timestamp);
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
