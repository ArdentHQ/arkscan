<?php

declare(strict_types=1);

namespace App\Http\Controllers\Concerns;

use App\Events\WalletVote;
use Illuminate\Support\Collection;

trait HandlesWalletWebhooks
{
    private function handleWalletVote(): void
    {
        $address = $this->getVote();
        if ($address === null) {
            return;
        }

        WalletVote::dispatch($address);
    }

    /** We use the same vote key/event so we can prevent repeat events for the same public key */
    private function handleWalletUnvote(): void
    {
        $address = $this->getUnvote();
        if ($address === null) {
            return;
        }

        WalletVote::dispatch($address);
    }

    private function getVote(): ?string
    {
        return (new Collection(request()->input('data.transaction.asset.votes')))
            ->filter(fn ($vote) => str_starts_with($vote, '+'))
            ->map(fn ($vote) => trim($vote, '+'))
            ->first();
    }

    private function getUnvote(): ?string
    {
        return (new Collection(request()->input('data.transaction.asset.votes')))
            ->filter(fn ($vote) => str_starts_with($vote, '-'))
            ->map(fn ($vote) => trim($vote, '-'))
            ->first();
    }
}
