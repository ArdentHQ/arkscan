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

    public function search(string $query): ?Builder
    {
        // Prevents finding wallets by transaction ID
        if ($this->is64CharsHexadecimalString($query)) {
            return null;
        }

        if ($this->couldBeAddress($query)) {
            // Exact match
            return Wallet::search(sprintf('"%s"', $query));
        } else {
            return Wallet::search($query);
        }
    }
}
