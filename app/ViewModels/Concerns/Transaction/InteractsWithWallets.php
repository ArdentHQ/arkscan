<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use App\DTO\MemoryWallet;

trait InteractsWithWallets
{
    public function recipient(): MemoryWallet
    {
        return MemoryWallet::fromAddress($this->transaction->recipientAddress());
    }
}
