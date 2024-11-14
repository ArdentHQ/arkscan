<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\Models\Wallet;
use App\ViewModels\WalletViewModel;
use ArkEcosystem\Crypto\Utils\AbiDecoder;

trait InteractsWithVotes
{
    public function voted(): ?WalletViewModel
    {
        if (! $this->isVote()) {
            return null;
        }

        $payload = $this->rawPayload();
        if ($payload === null) {
            return null;
        }

        $method = (new AbiDecoder())->decodeFunctionData($payload);

        if (count($method['args']) === 0) {
            return null;
        }

        /** @var string $address */
        $address = $method['args'][0];

        $wallet = Wallet::where('address', $address)->first();
        if ($wallet === null) {
            return null;
        }

        return new WalletViewModel($wallet);
    }
}
