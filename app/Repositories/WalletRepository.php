<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\WalletRepository as Contract;
use App\Models\Wallet;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

final class WalletRepository implements Contract
{
    use ValidatesTerm;

    /** @return Builder<Wallet> */
    public function allWithUsername(): Builder
    {
        return Wallet::whereNotNull('wallets.attributes->delegate->username');
    }

    /** @return Builder<Wallet> */
    public function allWithVote(): Builder
    {
        return Wallet::whereNotNull('attributes->vote')->orderBy('balance');
    }

    /** @return Builder<Wallet> */
    public function allWithPublicKey(): Builder
    {
        return Wallet::whereNotNull('public_key');
    }

    /** @return Builder<Wallet> */
    public function allWithMultiSignature(): Builder
    {
        return Wallet::whereNotNull('attributes->multiSignature');
    }

    public function findByAddress(string $address): Wallet
    {
        return Wallet::where('address', $address)->firstOrFail();
    }

    public function findByPublicKey(string $publicKey): Wallet
    {
        return Wallet::where('public_key', $publicKey)->firstOrFail();
    }

    /** @return Collection<int, Wallet> */
    public function findByPublicKeys(array $publicKeys): Collection
    {
        return Wallet::whereIn('public_key', $publicKeys)->get();
    }

    public function findByUsername(string $username, bool $caseSensitive = true): Wallet
    {
        if ($caseSensitive === false) {
            return Wallet::whereRaw(
                'lower(attributes::text)::jsonb @> lower(?)::jsonb',
                ['{"delegate":{"username":"'.$username.'"}}'],
            )->firstOrFail();
        }

        return Wallet::where('attributes->delegate->username', $username)->firstOrFail();
    }

    public function findByIdentifier(string $identifier): Wallet
    {
        $query =  Wallet::query();

        if ($this->couldBeAddress($identifier)) {
            $query->whereLower('address', $identifier);
        } elseif ($this->couldBePublicKey($identifier)) {
            $query->whereLower('public_key', $identifier);
        } elseif ($this->couldBeUsername($identifier)) {
            $query->orWhereRaw(
                'lower(attributes::text)::jsonb @> lower(?)::jsonb',
                ['{"delegate":{"username":"'.$identifier.'"}}'],
            );
        } else {
            $query->empty();
        }

        return $query->firstOrFail();
    }
}
