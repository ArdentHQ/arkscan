<?php

declare(strict_types=1);

namespace App\DTO;

final class Payment
{
    private string $amount;

    private string $recipient;

    public function __construct(string $amount, string $recipient)
    {
        $this->amount    = $amount;
        $this->recipient = $recipient;
    }

    public function amount(): string
    {
        return $this->amount;
    }

    public function recipient(): string
    {
        return $this->recipient;
    }
}
