<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Services\Cache\WalletCache;
use Illuminate\Support\Arr;
use Mattiasgeniar\Percentage\Percentage;

trait CanVote
{
    public function isVoting(): bool
    {
        return ! is_null(Arr::get($this->wallet, 'attributes.vote'));
    }

    public function vote(): ?self
    {
        if (is_null($this->wallet->public_key)) {
            return null;
        }

        $vote = Arr::get($this->wallet, 'attributes.vote');

        if (is_null($vote)) {
            return null;
        }

        $delegate = (new WalletCache())->getVote($vote);

        if (is_null($delegate)) {
            return null;
        }

        return new static($delegate);
    }

    public function votePercentage(): ?float
    {
        if (is_null($this->wallet->public_key)) {
            return null;
        }

        $vote = Arr::get($this->wallet, 'attributes.vote');

        if (is_null($vote)) {
            return null;
        }

        $delegate = (new WalletCache())->getVote($vote);

        if (is_null($delegate)) {
            return null;
        }

        if ((float) $delegate->attributes['delegate']['voteBalance'] === 0.0) {
            return null;
        }

        return Percentage::calculate($this->wallet->balance->toNumber(), (float) $delegate->attributes['delegate']['voteBalance']);
    }
}
