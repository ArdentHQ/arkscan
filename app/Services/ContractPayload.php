<?php

declare(strict_types=1);

namespace App\Services;

use ArkEcosystem\Crypto\Utils\Address;

final class ContractPayload
{
    public static function decodeAddress(string $bytes, int $offset = 0): string
    {
        $bytes        = hex2bin($bytes);
        $data         = substr($bytes, $offset, 32);
        $addressBytes = substr($data, 12, 20);
        $address      = Address::toChecksumAddress('0x'.bin2hex($addressBytes));

        return $address;
    }

    public static function decodeUnsignedInt(string $bytes, int $offset = 0): string
    {
        $bytes = hex2bin($bytes);
        $data  = substr($bytes, $offset, 32);
        $hex   = bin2hex($data);
        $value = gmp_import(hex2bin($hex), 1, GMP_MSW_FIRST | GMP_BIG_ENDIAN);

        return gmp_strval($value);
    }

    // public static function decodeSignedInt(string $bytes, int $bits = 32, int $offset = 0): string
    // {
    //     $bytes = hex2bin($bytes);
    //     $data  = substr($bytes, $offset, 32);
    //     $hex   = bin2hex($data);
    //     $value = gmp_import(hex2bin($hex), 1, GMP_MSW_FIRST | GMP_BIG_ENDIAN);
    //     if (gmp_testbit($value, $bits - 1)) {
    //         $value = gmp_sub($value, gmp_pow(2, $bits));
    //     }

    //     return gmp_strval($value);
    // }
}
