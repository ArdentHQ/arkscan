<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Services\MultiSignature;
use ArkEcosystem\Crypto\Identities\Address;
use Illuminate\Support\Arr;

trait InteractsWithMultiSignature
{
    public function multiSignatureAddress(): ?string
    {
        if (! $this->isMultiSignature()) {
            return null;
        }

        if (is_null($this->transaction->asset)) {
            return null;
        }

        return MultiSignature::address(
            Arr::get($this->transaction->asset, 'multiSignature.min', 0),
            Arr::get($this->transaction->asset, 'multiSignature.publicKeys', [])
        );
    }

    public function participants(): array
    {
        if (! $this->isMultiSignature()) {
            return [];
        }

        if (is_null($this->transaction->asset)) {
            return [];
        }

        return collect(Arr::get($this->transaction->asset, 'multiSignature.publicKeys', []))
            ->map(fn ($publicKey) => Address::fromPublicKey($publicKey))
            ->toArray();
    }
}
