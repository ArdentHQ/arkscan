<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use Brick\Math\RoundingMode;

trait HasPayload
{
    public function hasPayload(): bool
    {
        return $this->transaction->hasPayload();
    }

    public function rawPayload(): ?string
    {
        return $this->transaction->rawPayload();
    }

    public function utf8Payload(): ?string
    {
        return $this->transaction->utf8Payload();
    }

    public function formattedPayload(): ?string
    {
        return $this->transaction->formattedPayload();
    }

    public function parseReceiptError(): ?string
    {
        return $this->transaction->parseReceiptError();
    }

    /**
     * @return array<int, array{address: string, amount: float}>
     */
    public function multiPaymentRecipients(): array
    {
        return $this->transaction->multiPaymentRecipients();
    }
}
