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
            request()->input('data.to'),
        );

        CheckLatestWallet::dispatch();
        CheckLargestTransaction::dispatch();
    }
}
