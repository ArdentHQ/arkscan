<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Services\Cache\WalletCache;
use Brick\Math\RoundingMode;
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

        $validator = (new WalletCache())->getVote($vote);

        if (is_null($validator)) {
            return null;
        }

        return new static($validator);
    }

    public function votePercentage(): ?float
    {
        $vote = Arr::get($this->wallet, 'attributes.vote');

        if (is_null($vote)) {
            return null;
        }

        $validator = (new WalletCache())->getVote($vote);

        if (is_null($validator)) {
            return null;
        }

        if ((float) $validator->attributes['validatorVoteBalance'] === 0.0) {
            return null;
        }

        return $this->wallet->balance->valueOf()->multipliedBy(100)->dividedBy($validator->attributes['validatorVoteBalance'], 2, RoundingMode::DOWN)->toFloat();
    }
}
