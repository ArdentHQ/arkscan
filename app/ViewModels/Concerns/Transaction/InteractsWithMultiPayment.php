<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\DTO\Payment;
use Illuminate\Support\Arr;

trait InteractsWithMultiPayment
{
    public function payments(): array
    {
        if (! $this->isMultiPayment()) {
            return [];
        }

        if (is_null($this->transaction->asset)) {
            return [];
        }

        return collect(Arr::get($this->transaction->asset, 'payments', []))
            ->map(fn ($payment) => new Payment($this->transaction->timestamp, $payment))
            ->toArray();
    }

    public function recipientsCount(): int
    {
        if (! $this->isMultiPayment()) {
            return 0;
        }

        if (is_null($this->transaction->asset)) {
            return 0;
        }

        return count(Arr::get($this->transaction->asset, 'payments'));
    }
}
