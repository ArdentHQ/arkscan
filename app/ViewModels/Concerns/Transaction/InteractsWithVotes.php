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

        // We don't need to check for the presence of the `args` key or the length
        // of the array, as checking if the method is a vote ensures
        // that it will attempt to extract an address from the input.
        // We used to check the length of the arguments, but in the hypothetical case
        // where it cannot extract the address, it would throw an exception beforehand,
        // making the validation unnecessary.
        /** @var string $address */
        $address = $method['args'][0];

        $wallet = Wallet::where('address', $address)->first();
        if ($wallet === null) {
            return null;
        }

        return new WalletViewModel($wallet);
    }
}
