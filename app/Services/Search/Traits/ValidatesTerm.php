<?php

declare(strict_types=1);

namespace App\Services\Search\Traits;

use App\Enums\Constants;

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
        return strlen($term) === Constants::ADDRESS_LENGTH;
    }

    private function couldntBeAddress(string $term): bool
    {
        return strlen($term) > Constants::ADDRESS_LENGTH;
    }

    private function couldBePublicKey(string $term): bool
    {
        return strlen($term) === 66 && $this->isHexadecimalString($term);
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
}
