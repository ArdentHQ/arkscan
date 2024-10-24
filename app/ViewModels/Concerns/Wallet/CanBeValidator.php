<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Wallet;

use App\Facades\Network;
use App\Services\Cache\WalletCache;
use Illuminate\Support\Arr;

trait CanBeValidator
{
    public function isValidator(): bool
    {
        return Arr::has($this->wallet, 'attributes.validatorPublicKey');
    }

    public function isResigned(): bool
    {
        return Arr::get($this->wallet, 'attributes.validatorResigned', false) === true;
    }

    public function isStandby(): bool
    {
        return $this->rank() > Network::validatorCount();
    }

    public function isActive(): bool
    {
        if (! $this->isValidator()) {
            return false;
        }

        if ($this->isResigned()) {
            return false;
        }

        return ! $this->isStandby();
    }

    public function resignationId(): ?string
    {
        if (is_null($this->wallet->public_key)) {
            return null;
        }

        if (! $this->isResigned()) {
            return null;
        }

        return (new WalletCache())->getResignationId($this->wallet->address);
    }

    public function rank(): ?int
    {
        return Arr::get($this->wallet, 'attributes.validatorRank', 0);
    }
}
