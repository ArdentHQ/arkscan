<?php

declare(strict_types=1);

namespace App\Services\Transactions;

use App\Facades\Network;
use App\Models\Transaction;
use App\Services\Blockchain\NetworkStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

final class TransactionState
{
    private Transaction $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function isConfirmed(): bool
    {
        $block = Cache::remember(
            "block:{$this->transaction->block_id}",
            Carbon::now()->addMinutes(10),
            fn () => $this->transaction->block
        );

        if (is_null($block)) {
            return false;
        }

        $confirmations = NetworkStatus::height() - $block->height->toNumber();

        return $confirmations >= Network::confirmations();
    }
}
