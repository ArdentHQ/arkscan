<?php

declare(strict_types=1);

namespace App\Services\Search\Traits;

use App\Enums\SQLEnum;

trait ValidatesTerm
{
    private function couldBeTransactionID(string $term): bool
    {
        return $this->is64CharsHexadecimalString($term);
    }

    private function couldBeBlockID(string $term): bool
    {
        return $this->is64CharsHexadecimalString($term);
    }

    private function couldBeAddress(string $term): bool
    {
        return strlen($term) === 34;
    }

    private function couldBePublicKey(string $term): bool
    {
        return strlen($term) === 66 && $this->isHexadecimalString($term);
    }

    /**
     * Check if the query can be a username
     * Regex source: https://github.com/ArkEcosystem/core/blob/4e149f039b59da97d224db1c593059dbc8e0f385/packages/core-api/src/handlers/shared/schemas/username.ts.
     *
     * @return bool
     */
    private function couldBeUsername(string $term): bool
    {
        // "Known wallets" are not related to anything on-chain, they are set by ARK team and can be seen here: https://github.com/ArkEcosystem/common/tree/master/mainnet
        // They are not related to delegate usernames that are registered on-chain, meaning that they can be anything.
        // Therefore 30 character restriction is not something that's actively enforced for those names

        $regex = '/^[a-zA-Z0-9!@$&_.()\[\] ]+$/';

        return strlen($term) >= 1
            && strlen($term) <= 30
            && preg_match($regex, $term, $matches) > 0;
    }

    private function couldBeHeightValue(string $term): bool
    {
        $numericTerm = strval(filter_var($term, FILTER_VALIDATE_FLOAT, FILTER_FLAG_ALLOW_THOUSAND));

        return $this->isOnlyNumbers($numericTerm) && $this->numericTermIsInRange($numericTerm);
    }

    private function is64CharsHexadecimalString(string $term): bool
    {
        return $this->isOnlyNumbers($term)
            || (strlen($term) === 64 && $this->isHexadecimalString($term));
    }

    private function isOnlyNumbers(string $term): bool
    {
        return ctype_digit($term);
    }

    private function isHexadecimalString(string $term): bool
    {
        return ctype_xdigit($term);
    }

    /**
     * Validates that the numnber is smaller that the max size for a type integer
     * on pgsql. Searching for a bigger number will result in an SQL exception.
     *
     * @return bool
     */
    private function numericTermIsInRange(string $term): bool
    {
        return floatval($term) <= SQLEnum::INT4_MAXVALUE;
    }
}
