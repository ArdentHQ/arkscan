<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\TransactionRepository as Contract;
use App\Models\Transaction;
use Illuminate\Support\Collection;

final class TransactionRepository implements Contract
{
    public function allByWallet(string $address, string $publicKey): Collection
    {
        return Transaction::query()
            ->where(fn ($query) => $query->where('sender_public_key', $publicKey))
            ->orWhere(fn ($query) => $query->where('recipient_id', $address))
            ->orWhere(fn ($query) => $query->whereJsonContains('asset->payments', [['recipientId' => $address]]))
            ->get();
    }

    public function allBySender(string $publicKey): Collection
    {
        return Transaction::where('sender_public_key', $publicKey)->get();
    }

    public function allByRecipient(string $address): Collection
    {
        return Transaction::query()
            ->orWhere(fn ($query) => $query->where('recipient_id', $address))
            ->orWhere(fn ($query) => $query->whereJsonContains('asset->payments', [['recipientId' => $address]]))
            ->get();
    }

    public function findById(string $id): Transaction
    {
        return Transaction::findOrFail($id);
    }
}
