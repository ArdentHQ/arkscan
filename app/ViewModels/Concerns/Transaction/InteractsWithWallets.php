<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\DTO\MemoryWallet;

trait InteractsWithWallets
{
    public function sender(): MemoryWallet
    {
        return MemoryWallet::fromPublicKey($this->transaction->sender_public_key);
    }

    public function recipient(): MemoryWallet
    {
        return MemoryWallet::fromAddress($this->transaction->recipientAddress());
    }
}
