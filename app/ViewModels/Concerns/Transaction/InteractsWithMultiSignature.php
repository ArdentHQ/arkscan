<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Models\Wallet;
use App\Services\MultiSignature;
use App\ViewModels\WalletViewModel;
use Illuminate\Support\Arr;

trait InteractsWithMultiSignature
{
    public function multiSignatureWallet(): ?WalletViewModel
    {
        $address = $this->multiSignatureAddress();

        if (is_null($address)) {
            return null;
        }

        return new WalletViewModel(Wallet::where('address', $address)->firstOrFail());
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
            ->map(fn ($address) => Wallet::where('public_key', $address)->firstOrFail())
            ->map(fn ($wallet)  => new WalletViewModel($wallet))
            ->toArray();
    }
}
