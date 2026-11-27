<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Events\NewTransaction;
use App\Jobs\Webhooks\CheckLargestTransaction;
use App\Jobs\Webhooks\CheckLatestWallet;

trait HandlesTransactionWebhooks
{
    private function handleTransactionApplied(): void
    {
        NewTransaction::dispatch(
            null,
            request()->input('data.senderPublicKey'),
            request()->input('data.recipientId'),
            ...$this->multiPaymentRecipients(),
        );

        CheckLatestWallet::dispatch();
        CheckLargestTransaction::dispatch();
    }

    /** @return array<string> */
    private function multiPaymentRecipients(): array
    {
        $payments = request()->input('data.asset.payments');

        if (! is_array($payments)) {
            return [];
        }

        $recipients = array_filter(
            array_map(fn ($payment) => is_array($payment) ? ($payment['recipientId'] ?? null) : null, $payments),
            fn ($recipient) => is_string($recipient) && $recipient !== '' && $recipient !== request()->input('data.recipientId'),
        );

        return array_values(array_unique($recipients));
    }
}
