<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Models\Wallet;
use App\Services\MultiSignature;
use App\ViewModels\WalletViewModel;
use ArkEcosystem\Crypto\Identities\Address;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

trait InteractsWithMultiSignature
{
    public function multiSignatureWallet(): ?WalletViewModel
    {
        $address = $this->multiSignatureAddress();

        if (is_null($address)) {
            return null;
        }

        return new WalletViewModel(
            Cache::remember("multiSignatureWallet:$address", 60, fn () => Wallet::where('address', $address)->firstOrFail())
        );
    }

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
            ->map(fn ($address)   => Cache::remember("participant:$address", 60, fn () => Wallet::where('address', $address)->firstOrFail()))
            ->map(fn ($wallet)    => new WalletViewModel($wallet))
            ->toArray();
    }

    public function multiSignatureMinimum(): ?int
    {
        if (! $this->isMultiSignature()) {
            return null;
        }

        if (is_null($this->transaction->asset)) {
            return null;
        }

        return Arr::get($this->transaction->asset, 'multiSignature.min', 0);
    }

    public function multiSignatureParticipantCount(): ?int
    {
        if (! $this->isMultiSignature()) {
            return null;
        }

        if (is_null($this->transaction->asset)) {
            return null;
        }

        return count(Arr::get($this->transaction->asset, 'multiSignature.publicKeys', []));
    }
}
