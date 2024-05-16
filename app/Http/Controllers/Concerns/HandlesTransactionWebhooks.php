<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Events\NewTransaction;
use Illuminate\Support\Facades\Cache;

trait HandlesTransactionWebhooks
{
    private function handleTransactionApplied(): void
    {
        $lock = Cache::lock('webhooks:transaction:applied', config('arkscan.webhooks.transaction-applied.ttl', 8));
        if ($lock->get() === false) {
            return;
        }

        NewTransaction::dispatch();
    }

    private function handleSenderTransactionApplied(): void
    {
        $publicKey = request()->input('data.senderPublicKey');

        $lock = Cache::lock('webhooks:transaction:applied:'.$publicKey, config('arkscan.webhooks.transaction-applied.ttl', 8));
        if ($lock->get() === false) {
            return;
        }

        NewTransaction::dispatch($publicKey);
    }

    private function handleRecipientTransactionApplied(): void
    {
        $address = request()->input('data.recipientId');

        $lock = Cache::lock('webhooks:transaction:applied:'.$address, config('arkscan.webhooks.transaction-applied.ttl', 8));
        if ($lock->get() === false) {
            return;
        }

        // Recipient Address since we can't easily get the public key
        NewTransaction::dispatch($address);
    }
}
