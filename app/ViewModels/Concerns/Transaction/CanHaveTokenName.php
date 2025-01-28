<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Services\Cache\TokenTransferCache;

trait CanHaveTokenName
{
    public function tokenName(): ?string
    {
        $cache = new TokenTransferCache();
        $contractId = $this->contractId();

        if (! $cache->hasTokenName($contractId)) {
            return null;
        }

        return $cache->getTokenName($contractId);
    }

    public function contractId(): ?string
    {
        if ($this->isContractDeployment()) {
            return $this->transaction->receipt->deployed_contract_address;
        }

        if ($this->isTokenTransfer()) {
            return $this->transaction->recipient_address;
        }

        return null;
    }
}
