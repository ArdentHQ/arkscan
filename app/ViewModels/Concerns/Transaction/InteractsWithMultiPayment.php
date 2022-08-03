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

        /** @var array<int, array<string, string>> */
        $payments = Arr::get($this->transaction->asset, 'payments', []);

        return collect($payments)
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
