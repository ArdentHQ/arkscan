<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Events\WalletVote;
use Illuminate\Support\Facades\Cache;

trait HandlesWalletWebhooks
{
    private function handleWalletVote(): void
    {
        $publicKey = $this->getVote();
        if ($publicKey === null) {
            return;
        }

        $lock = Cache::lock('webhooks:wallet:vote:'.$publicKey, config('arkscan.webhooks.wallet-vote.ttl', 8));
        if ($lock->get() === false) {
            return;
        }

        WalletVote::dispatch($publicKey);
    }

    /** We use the same vote key/event so we can prevent repeat events for the same public key */
    private function handleWalletUnvote(): void
    {
        $publicKey = $this->getUnvote();
        if ($publicKey === null) {
            return;
        }

        $lock = Cache::lock('webhooks:wallet:vote:'.$publicKey, config('arkscan.webhooks.wallet-vote.ttl', 8));
        if ($lock->get() === false) {
            return;
        }

        WalletVote::dispatch($publicKey);
    }

    private function getVote(): ?string
    {
        return collect(request()->input('data.asset.votes'))
            ->filter(fn ($vote) => str_starts_with($vote, '+'))
            ->map(fn ($vote) => trim($vote, '+'))
            ->first();
    }

    private function getUnvote(): ?string
    {
        return collect(request()->input('data.asset.votes'))
            ->filter(fn ($vote) => str_starts_with($vote, '-'))
            ->map(fn ($vote) => trim($vote, '-'))
            ->first();
    }
}
