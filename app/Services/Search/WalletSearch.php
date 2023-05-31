<?php

declare(strict_types=1);

namespace App\Services\Search;

use App\Contracts\Search;
use App\Models\Wallet;
use App\Services\Search\Traits\ValidatesTerm;
use Laravel\Scout\Builder;

final class WalletSearch implements Search
{
    use ValidatesTerm;

    public function search(string $query, int $limit): Builder | null
    {
        // Prevents finding wallets by transaction ID
        if ($this->is64CharsHexadecimalString($query)) {
            return null;
        }

        if ($this->couldBeAddress($query)) {
            // Quoted so it gets the exact match
            return Wallet::search(sprintf('"%s"', $query))->take($limit);
        }

        return Wallet::search($query)->take($limit);
    }
}
