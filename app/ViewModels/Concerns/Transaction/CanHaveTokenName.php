<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Services\Cache\ContractCache;

trait CanHaveTokenName
{
    public function tokenName(): ?string
    {
        $cache      = new ContractCache();
        $contractId = $this->contractId();
        if ($contractId === null) {
            return null;
        }

        if (! $cache->hasTokenName($contractId)) {
            return null;
        }

        return $cache->getTokenName($contractId);
    }

    public function contractId(): ?string
    {
        if ($this->isContractDeployment()) {
            if ($this->transaction->receipt === null) {
                return null;
            }

            return $this->transaction->receipt->deployed_contract_address;
        }

        if ($this->isTokenTransfer()) {
            return $this->transaction->recipient_address;
        }

        return null;
    }
}
