<?php

declare(strict_types=1);

namespace App\Services;

use ArkEcosystem\Crypto\Utils\Address;

final class ContractPayload
{
    public static function decodeAddress(string $bytes, int $offset = 0): string
    {
        $bytes = hex2bin($bytes);
        if ($bytes === false) {
            return '';
        }

        $data         = substr($bytes, $offset, 32);
        $addressBytes = substr($data, 12, 20);
        $address      = Address::toChecksumAddress('0x'.bin2hex($addressBytes));

        return $address;
    }

    public static function decodeUnsignedInt(string $bytes, int $offset = 0): string
    {
        $bytes = hex2bin($bytes);
        if ($bytes === false) {
            return '';
        }

        $data  = substr($bytes, $offset, 32);
        $value = gmp_import($data, 1, GMP_MSW_FIRST | GMP_BIG_ENDIAN);

        return gmp_strval($value);
    }
}
