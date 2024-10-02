<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Events\NewTransaction;
use App\Jobs\Webhooks\CheckLatestWallet;
use App\Jobs\Webhooks\CheckLargestTransaction;

trait HandlesTransactionWebhooks
{
    private function handleTransactionApplied(): void
    {
        NewTransaction::dispatch();
        CheckLatestWallet::dispatch();
        CheckLargestTransaction::dispatch();
    }

    private function handleSenderTransactionApplied(): void
    {
        NewTransaction::dispatch(request()->input('data.senderPublicKey'));
    }

    private function handleRecipientTransactionApplied(): void
    {
        // Recipient Address since we can't easily get the public key
        NewTransaction::dispatch(request()->input('data.recipientId'));
    }
}
