<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Scopes\ValidatorResignationScope;
use App\Models\Transaction;
use App\Services\Cache\CommandsCache;
use App\Services\Cache\WalletCache;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class CacheResignationIds implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle(WalletCache $walletCache, CommandsCache $commandsCache): void
    {
        $currencyLastUpdated = $commandsCache->getResignationIdsLastUpdated();

        $transactions = Transaction::select('sender_public_key', 'hash', 'timestamp')
            ->withScope(ValidatorResignationScope::class)
            ->where('timestamp', '>', $currencyLastUpdated)
            ->get();

        if ($transactions->isEmpty()) {
            return;
        }

        foreach ($transactions as $transaction) {
            $walletCache->setResignationId($transaction->sender_public_key, $transaction->hash);
        }

        $commandsCache->setResignationIdsLastUpdated(Carbon::now()->getTimestampMs());
    }
}
