<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Models\MultiPayment;
use Illuminate\Database\Eloquent\Collection;

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

    /**
     * @return Collection<int, MultiPayment>
     */
    public function multiPaymentRecipients(): Collection
    {
        return $this->transaction->multiPaymentRecipients;
    }
}
