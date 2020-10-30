<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Mattiasgeniar\Percentage\Percentage;

trait CanVote
{
    public function isVoting(): bool
    {
        return ! is_null(Arr::get($this->wallet, 'attributes.vote'));
    }

    public function vote(): ?self
    {
        if (! Arr::has($this->wallet, 'attributes.vote')) {
            return null;
        }

        $wallet = Cache::get('votes.'.Arr::get($this->wallet, 'attributes.vote'));

        if (is_null($wallet)) {
            return null;
        }

        return new static($wallet);
    }

    public function votePercentage(): ?float
    {
        if (! Arr::has($this->wallet, 'attributes.vote')) {
            return null;
        }

        $delegate = Cache::get('votes.'.Arr::get($this->wallet, 'attributes.vote'));

        if (is_null($delegate)) {
            return null;
        }

        return Percentage::calculate($this->wallet->balance->toNumber(), (float) $delegate->attributes['delegate']['voteBalance']);
    }
}
