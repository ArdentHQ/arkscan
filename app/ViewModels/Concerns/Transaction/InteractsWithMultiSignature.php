<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\DTO\MemoryWallet;
use App\Facades\Wallets;
use App\ViewModels\WalletViewModel;
use ArkEcosystem\Crypto\Identities\Address;
use Illuminate\Support\Arr;

trait InteractsWithMultiSignature
{
    public function multiSignatureWallet(): ?WalletViewModel
    {
        $address = $this->multiSignatureAddress();

        if (is_null($address)) {
            return null;
        }

        return new WalletViewModel(Wallets::findByAddress($address));
    }

    public function multiSignatureAddress(): ?string
    {
        if (! $this->isMultiSignature()) {
            return null;
        }

        if (is_null($this->transaction->asset)) {
            return null;
        }

        if (Arr::has($this->transaction->asset, 'multiSignature')) {
            return Address::fromMultiSignatureAsset(
                Arr::get($this->transaction->asset, 'multiSignature.min', 0),
                Arr::get($this->transaction->asset, 'multiSignature.publicKeys', [])
            );
        }

        return $this->transaction->sender->address;
    }

    public function participants(): array
    {
        if (! $this->isMultiSignature()) {
            return [];
        }

        if (is_null($this->transaction->asset)) {
            return [];
        }

        $participants = null;
        if (Arr::has($this->transaction->asset, 'multiSignatureLegacy')) {
            $participants = collect(Arr::get($this->transaction->asset, 'multiSignatureLegacy.keysgroup', []))
                ->map(fn ($publicKey) => substr($publicKey, 1));
        } else {
            $participants = collect(Arr::get($this->transaction->asset, 'multiSignature.publicKeys', []));
        }

        return $participants->map(fn ($publicKey) => Address::fromPublicKey($publicKey))
            ->map(fn ($address)                   => MemoryWallet::fromAddress($address))
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

        if (Arr::has($this->transaction->asset, 'multiSignatureLegacy')) {
            return Arr::get($this->transaction->asset, 'multiSignatureLegacy.min', 0);
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

        if (Arr::has($this->transaction->asset, 'multiSignatureLegacy')) {
            return count(Arr::get($this->transaction->asset, 'multiSignatureLegacy.keysgroup', []));
        }

        return count(Arr::get($this->transaction->asset, 'multiSignature.publicKeys', []));
    }
}
