<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Facades\Network;
use Illuminate\Support\Arr;

trait HasType
{
    public function hasSecondSignature(): bool
    {
        return Arr::has($this->wallet->attributes, 'secondPublicKey');
    }

    public function isKnown(): bool
    {
        return ! is_null($this->findWalletByKnown());
    }

    public function isOwnedByTeam(): bool
    {
        if (! $this->isKnown()) {
            return false;
        }

        return optional($this->findWalletByKnown())['type'] === 'team';
    }

    public function isOwnedByExchange(): bool
    {
        if (! $this->isKnown()) {
            return false;
        }

        return optional($this->findWalletByKnown())['type'] === 'exchange';
    }

    private function findWalletByKnown(): ?array
    {
        return collect(Network::knownWallets())->firstWhere('address', $this->wallet->address);
    }
}
