<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\TransactionRepository;
use App\Models\Transaction;
use App\Repositories\Concerns\ManagesCache;
use Illuminate\Cache\TaggedCache;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

final class TransactionRepositoryWithCache implements TransactionRepository
{
    use ManagesCache;

    public function __construct(private TransactionRepository $transactions)
    {
    }

    public function allByWallet(string $address, string $publicKey): Collection
    {
        return $this->remember(fn () => $this->transactions->allByWallet($address, $publicKey));
    }

    public function allBySender(string $publicKey): Collection
    {
        return $this->remember(fn () => $this->transactions->allBySender($publicKey));
    }

    public function allByRecipient(string $address): Collection
    {
        return $this->remember(fn () => $this->transactions->allByRecipient($address));
    }

    public function findById(string $id): Transaction
    {
        return $this->remember(fn () => $this->transactions->findById($id));
    }

    private function getCache(): TaggedCache
    {
        return Cache::tags('transactions');
    }
}
