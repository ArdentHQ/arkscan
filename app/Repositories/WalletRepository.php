<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\WalletRepository as Contract;
use App\Models\Wallet;
use App\Services\Search\Traits\ValidatesTerm;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class WalletRepository implements Contract
{
    use ValidatesTerm;

    public function allWithUsername(): Builder
    {
        return Wallet::whereNotNull('wallets.attributes->username');
    }

    public function allWithValidatorPublicKey(): Builder
    {
        return Wallet::whereNotNull('wallets.attributes->validatorPublicKey');
    }

    public function allWithVote(): Builder
    {
        return Wallet::whereNotNull('attributes->vote')->orderBy('balance');
    }

    public function allWithPublicKey(): Builder
    {
        return Wallet::whereNotNull('public_key');
    }

    public function findByAddress(string $address): Wallet
    {
        return Wallet::where('address', $address)->firstOrFail();
    }

    public function findByPublicKey(string $publicKey): Wallet
    {
        return Wallet::where('public_key', $publicKey)->firstOrFail();
    }

    public function findByPublicKeys(array $publicKeys): Collection
    {
        return Wallet::whereIn('public_key', $publicKeys)->get();
    }

    public function findByUsername(string $username, bool $caseSensitive = true): Wallet
    {
        if ($caseSensitive === false) {
            $username = substr(DB::getPdo()->quote($username), 1, -1);

            return Wallet::whereRaw('lower(attributes::text)::jsonb @> lower(\'{"username":"'.$username.'"}\')::jsonb')
                ->firstOrFail();
        }

        return Wallet::where('attributes->username', $username)->firstOrFail();
    }

    public function findByIdentifier(string $identifier): Wallet
    {
        $query =  Wallet::query();

        if ($this->couldBeAddress($identifier)) {
            $query->whereLower('address', $identifier);
        } elseif ($this->couldBePublicKey($identifier)) {
            $query->whereLower('public_key', $identifier);
        } elseif ($this->couldBeUsername($identifier)) {
            $username = substr(DB::getPdo()->quote($identifier), 1, -1);
            $query->orWhereRaw('lower(attributes::text)::jsonb @> lower(\'{"username":"'.$username.'"}\')::jsonb');
        } else {
            $query->empty();
        }

        return $query->firstOrFail();
    }
}
