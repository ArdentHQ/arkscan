<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Events\WalletVote;
use Illuminate\Support\Collection;

trait HandlesWalletWebhooks
{
    private function handleWalletVotes(): void
    {
        $publicKeys = (new Collection(request()->input('data.transaction.asset.votes')))
            ->map(fn ($vote) => trim($vote, '+-'));

        if (count($publicKeys) === 0) {
            return;
        }

        WalletVote::dispatch(...$publicKeys);
    }
}
