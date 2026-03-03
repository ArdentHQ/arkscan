<?php

declare(strict_types=1);

namespace Tests\Stubs;

use App\Contracts\TransactionRepository;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class TransactionRepositoryStub implements TransactionRepository
{
    public int $allByWalletCalls = 0;
    public int $allBySenderCalls = 0;
    public int $allByRecipientCalls = 0;
    public int $findByHashCalls = 0;

    public function __construct(private Transaction $transaction)
    {
        //
    }

    public function allByWallet(string $address, string $publicKey): Collection
    {
        $this->allByWalletCalls++;

        return collect([$address, $publicKey]);
    }

    public function allBySender(string $publicKey): Collection
    {
        $this->allBySenderCalls++;

        return collect([$publicKey]);
    }

    public function allByRecipient(string $address): Collection
    {
        $this->allByRecipientCalls++;

        return collect([$address]);
    }

    public function findByHash(string $hash): Transaction
    {
        $this->findByHashCalls++;

        return $this->transaction;
    }
}
