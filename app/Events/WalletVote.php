<?php

declare(strict_types=1);

namespace App\Events;

final class WalletVote extends WebsocketEvent
{
    public const CHANNEL = 'wallet-vote';

    protected function uniqueTimeout(): int
    {
        return config('arkscan.webhooks.wallet-vote.ttl', 8);
    }
}
