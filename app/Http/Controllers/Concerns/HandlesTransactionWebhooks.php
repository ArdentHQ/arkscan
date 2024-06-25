<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Events\NewTransaction;
use App\Events\Statistics\TransactionDetails;
use App\Events\Statistics\UniqueAddresses;
use App\Services\Addresses\Aggregates\LatestWalletAggregate;
use App\Services\Cache\TransactionCache;
use App\Services\Transactions\Aggregates\LargestTransactionAggregate;

trait HandlesTransactionWebhooks
{
    private function handleTransactionApplied(): void
    {
        NewTransaction::dispatch();

        $this->checkLatestWallet();
        $this->checkLargestTransaction();
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

    private function checkLatestWallet(): void
    {
        $latestWallet = (new LatestWalletAggregate())->aggregate();
        if ($latestWallet === null) {
            return;
        }

        UniqueAddresses::dispatch();
    }

    private function checkLargestTransaction(): void
    {
        $cache = new TransactionCache();
        $largestTransaction = (new LargestTransactionAggregate())->aggregate();
        if ($largestTransaction === null) {
            return;
        }

        if ($cache->getLargestIdByAmount() === $largestTransaction->id) {
            return;
        }

        $cache->setLargestIdByAmount($largestTransaction->id);

        TransactionDetails::dispatch();
    }
}
