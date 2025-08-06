<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Scopes\ValidatorResignationScope;
use App\Models\Transaction;
use App\Services\Cache\WalletCache;
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

    public function handle(WalletCache $cache): void
    {
        $transactions = Transaction::select('sender_public_key', 'hash')
            ->withScope(ValidatorResignationScope::class)
            ->get();

        foreach ($transactions as $transaction) {
            $cache->setResignationId($transaction->sender_public_key, $transaction->hash);
        }
    }
}
